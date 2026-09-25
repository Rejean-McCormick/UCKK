#requires -Version 7.0
[CmdletBinding()]
param(
    [string]$MoodleRoot = 'C:\mycode\UCKK\moodle\moodle\public',
    [string]$InventoryPath = 'C:\mycode\UCKK\uckk-import\uckkarchive\uckk_inventory.json',
    [string]$OriginalsDir = 'C:\mycode\UCKK\uckk-import\uckkarchive\originals',
    [ValidateSet('DryRun','Apply')]
    [string]$Mode = 'DryRun',
    [int]$CmId = 0,
    [int]$ArchiveId = 0,
    [int]$UserId = 0,
    [switch]$AllowMissingFiles,
    [switch]$UpdateMetadata,
    [switch]$ForceNewVersion,
    [int]$Offset = 0,
    [int]$Limit = 0,
    [string]$PhpPath = 'C:\php\8.4\php.exe',
    [switch]$NoAutoArchive
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

$LegacyNamePattern = '(?i)' + [regex]::Escape('Abstract') + '\s+' + [regex]::Escape('Wiki') + '\s+' + [regex]::Escape('Architect')
$LegacyAliasPattern = '(?<![A-Za-z0-9])' + ([char]65) + ([char]87) + ([char]65) + '(?![A-Za-z0-9])'

function Write-Section([string]$Text) {
    Write-Host ''
    Write-Host ('=' * 72) -ForegroundColor Cyan
    Write-Host $Text -ForegroundColor Cyan
    Write-Host ('=' * 72) -ForegroundColor Cyan
}

function Resolve-PhpPath {
    param([string]$Preferred)

    if ($Preferred -and (Test-Path -LiteralPath $Preferred -PathType Leaf)) {
        return (Resolve-Path -LiteralPath $Preferred).Path
    }

    $cmd = Get-Command php -ErrorAction SilentlyContinue
    if ($cmd) {
        return $cmd.Source
    }

    throw "PHP introuvable. Attendu: $Preferred"
}

function Get-DocxLegacyMentions {
    param([Parameter(Mandatory)][string]$Path)

    Add-Type -AssemblyName System.IO.Compression -ErrorAction SilentlyContinue
    Add-Type -AssemblyName System.IO.Compression.FileSystem -ErrorAction SilentlyContinue

    $hits = [System.Collections.Generic.List[string]]::new()
    $stream = [System.IO.File]::Open($Path, [System.IO.FileMode]::Open, [System.IO.FileAccess]::Read, [System.IO.FileShare]::Read)
    try {
        $zip = [System.IO.Compression.ZipArchive]::new($stream, [System.IO.Compression.ZipArchiveMode]::Read, $false)
        try {
            foreach ($entry in $zip.Entries) {
                if (-not $entry.FullName.EndsWith('.xml', [System.StringComparison]::OrdinalIgnoreCase)) {
                    continue
                }

                $entryStream = $entry.Open()
                try {
                    $reader = [System.IO.StreamReader]::new($entryStream, [System.Text.Encoding]::UTF8, $true)
                    try {
                        $text = $reader.ReadToEnd()
                    }
                    finally {
                        $reader.Dispose()
                    }
                }
                finally {
                    $entryStream.Dispose()
                }

                if ($text -match $script:LegacyNamePattern) {
                    $hits.Add("$($entry.FullName): ancien nom complet")
                }
                if ($text -match $script:LegacyAliasPattern) {
                    $hits.Add("$($entry.FullName): ancien alias")
                }
            }
        }
        finally {
            $zip.Dispose()
        }
    }
    finally {
        $stream.Dispose()
    }

    return @($hits)
}

function Invoke-ImporterPhp {
    param(
        [Parameter(Mandatory)][string]$RunMode,
        [Parameter(Mandatory)][string]$Php,
        [Parameter(Mandatory)][string]$Bootstrap
    )

    $args = @(
        $Bootstrap,
        "--inventory=$InventoryPath",
        "--originals=$OriginalsDir",
        "--mode=$RunMode",
        "--cmid=$CmId",
        "--archiveid=$ArchiveId",
        "--userid=$UserId",
        "--offset=$Offset",
        "--limit=$Limit"
    )

    if ($AllowMissingFiles) { $args += '--allowmissingfiles=1' }
    if ($UpdateMetadata)    { $args += '--updatemetadata=1' }
    if ($ForceNewVersion)   { $args += '--forcenewversion=1' }
    if ($NoAutoArchive)     { $args += '--noautoarchive=1' }

    & $Php @args | Out-Host
    $exitCode = [int]$LASTEXITCODE
    return $exitCode
}

Write-Section 'UCKK — IMPORT MÉDIATHÈQUE / PRÉVOL'

$PhpPath = Resolve-PhpPath -Preferred $PhpPath
$MoodleRoot = (Resolve-Path -LiteralPath $MoodleRoot).Path
$InventoryPath = (Resolve-Path -LiteralPath $InventoryPath).Path
$OriginalsDir = (Resolve-Path -LiteralPath $OriginalsDir).Path

$Bootstrap = Join-Path $MoodleRoot 'tools\uckk-ops\import\import_uckkarchive_media.php'
if (-not (Test-Path -LiteralPath $Bootstrap -PathType Leaf)) {
    throw "Bootstrapper PHP introuvable: $Bootstrap"
}

$inventoryRaw = Get-Content -LiteralPath $InventoryPath -Raw -Encoding UTF8
if ($inventoryRaw -match $LegacyNamePattern -or $inventoryRaw -match $LegacyAliasPattern) {
    throw "Le JSON d'inventaire contient encore l'ancien nom ou son ancien alias."
}

$inventory = $inventoryRaw | ConvertFrom-Json -Depth 100
if ($inventory.inventory_metadata.target_system -ne 'mod_uckkarchive') {
    throw "target_system invalide: $($inventory.inventory_metadata.target_system)"
}

$items = @($inventory.files)
if ($Offset -gt 0) {
    $items = @($items | Select-Object -Skip $Offset)
}
if ($Limit -gt 0) {
    $items = @($items | Select-Object -First $Limit)
}

if ($items.Count -eq 0) {
    throw "Aucune entrée sélectionnée dans l'inventaire."
}

$supported = @('.docx', '.pdf')
$preflightRows = [System.Collections.Generic.List[object]]::new()
$legacyHits = [System.Collections.Generic.List[string]]::new()
$missing = [System.Collections.Generic.List[string]]::new()
$badExtensions = [System.Collections.Generic.List[string]]::new()
$seenProposed = @{}

foreach ($item in $items) {
    $ops = $item.file_operations
    $proposed = [string]$ops.proposed_filename
    $original = [string]$ops.original_filename

    if ([string]::IsNullOrWhiteSpace($proposed)) {
        throw "Une entrée n'a pas de proposed_filename."
    }

    if ($seenProposed.ContainsKey($proposed.ToLowerInvariant())) {
        throw "proposed_filename dupliqué: $proposed"
    }
    $seenProposed[$proposed.ToLowerInvariant()] = $true

    $path = Join-Path $OriginalsDir $proposed
    if (-not (Test-Path -LiteralPath $path -PathType Leaf) -and $original) {
        $fallback = Join-Path $OriginalsDir $original
        if (Test-Path -LiteralPath $fallback -PathType Leaf) {
            $path = $fallback
        }
    }

    $exists = Test-Path -LiteralPath $path -PathType Leaf
    $ext = [System.IO.Path]::GetExtension($proposed).ToLowerInvariant()
    if ($ext -notin $supported) {
        $badExtensions.Add($proposed)
    }

    $sha = $null
    $size = 0
    if ($exists) {
        $file = Get-Item -LiteralPath $path
        $size = [int64]$file.Length
        $sha = (Get-FileHash -LiteralPath $path -Algorithm SHA256).Hash

        if ($ext -eq '.docx') {
            $hits = @(Get-DocxLegacyMentions -Path $path)
            foreach ($hit in $hits) {
                $legacyHits.Add("$proposed -> $hit")
            }
        }
    }
    else {
        $missing.Add($proposed)
    }

    $preflightRows.Add([pscustomobject]@{
        title = [string]$item.uckkarchive_media.title
        proposed_filename = $proposed
        exists = $exists
        size = $size
        sha256 = $sha
        status = [string]$item.uckkarchive_media.status
        visibility = [string]$item.uckkarchive_media.visibility
        audience = [string]$item.uckkarchive_media.audiencesuitability
        tags = @($item.uckkarchive_media_tags).Count
        advisories = @($item.uckkarchive_content_advisories).Count
    })
}

if ($badExtensions.Count -gt 0) {
    throw "Extensions non supportées: $($badExtensions -join ', ')"
}
if ($legacyHits.Count -gt 0) {
    Write-Host 'Références legacy détectées:' -ForegroundColor Red
    $legacyHits | ForEach-Object { Write-Host "  $_" -ForegroundColor Red }
    throw "Import bloqué: l'ancien nom ou son ancien alias est encore présent dans les originaux."
}
if ($missing.Count -gt 0 -and -not $AllowMissingFiles) {
    Write-Host 'Fichiers manquants:' -ForegroundColor Red
    $missing | ForEach-Object { Write-Host "  $_" -ForegroundColor Red }
    throw 'Import bloqué: des originaux sont manquants.'
}

$reportDir = Join-Path (Split-Path -Parent $InventoryPath) 'reports'
New-Item -ItemType Directory -Force -Path $reportDir | Out-Null
$stamp = Get-Date -Format 'yyyyMMdd_HHmmss'
$reportPath = Join-Path $reportDir "uckkarchive_media_preflight_$stamp.json"

$summary = [ordered]@{
    generated_at = (Get-Date).ToString('o')
    mode = $Mode
    target_system = $inventory.inventory_metadata.target_system
    inventory_version = $inventory.inventory_metadata.version
    inventory_path = $InventoryPath
    originals_dir = $OriginalsDir
    selected_count = $items.Count
    existing_files = @($preflightRows | Where-Object { $_.exists }).Count
    missing_files = $missing.Count
    legacy_name_hits = $legacyHits.Count
    supported_extensions = $supported
    rows = $preflightRows
}
$summary | ConvertTo-Json -Depth 12 | Set-Content -LiteralPath $reportPath -Encoding UTF8

Write-Host "Inventaire : $InventoryPath"
Write-Host "Originaux  : $OriginalsDir"
Write-Host "Entrées    : $($items.Count)"
Write-Host "Présents   : $(@($preflightRows | Where-Object { $_.exists }).Count)"
Write-Host "Legacy     : $($legacyHits.Count)"
Write-Host "Rapport    : $reportPath"

Write-Section 'DRY-RUN MOODLE'
$dryExit = Invoke-ImporterPhp -RunMode 'DryRun' -Php $PhpPath -Bootstrap $Bootstrap
if ($dryExit -ne 0) {
    throw "Dry-run Moodle échoué (exit=$dryExit). Aucune écriture effectuée."
}

if ($Mode -eq 'DryRun') {
    Write-Host ''
    Write-Host 'PRÉVOL + DRY-RUN RÉUSSIS. Aucune écriture effectuée.' -ForegroundColor Green
    exit 0
}

Write-Section 'APPLICATION MOODLE'
$applyExit = Invoke-ImporterPhp -RunMode 'Apply' -Php $PhpPath -Bootstrap $Bootstrap
if ($applyExit -ne 0) {
    throw "Import Moodle échoué (exit=$applyExit)."
}

Write-Host ''
Write-Host 'IMPORT TERMINÉ.' -ForegroundColor Green
Write-Host 'Les originaux ont été remis à Moodle File API; ils ne sont pas copiés dans public/.' -ForegroundColor Green
