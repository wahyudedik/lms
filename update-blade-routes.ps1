# PowerShell script to update hardcoded siswa/guru routes to dynamic role-aware routes
# Usage: .\update-blade-routes.ps1

Write-Host "Updating Blade views with dynamic role routes..." -ForegroundColor Cyan

$updatedCount = 0

# Function to update a single file
function Update-BladeFile {
    param([string]$FilePath)

    Write-Host "Processing: $FilePath" -ForegroundColor Yellow

    # Read file content
    $content = Get-Content $FilePath -Raw
    $originalContent = $content

    # Replace siswa routes with parameters
    $content = $content -replace "route\('siswa\.([^']+)',", "route(auth()->user()->getRolePrefix() . '.$1',"

    # Replace guru routes with parameters
    $content = $content -replace "route\('guru\.([^']+)',", "route(auth()->user()->getRolePrefix() . '.$1',"

    # Only write if content changed
    if ($content -ne $originalContent) {
        # Backup original file
        Copy-Item $FilePath "$FilePath.bak" -Force

        # Write updated content
        Set-Content $FilePath $content -NoNewline

        Write-Host "✓ Updated: $FilePath" -ForegroundColor Green
        return $true
    }

    return $false
}

# Update siswa views
Write-Host "`n=== Updating Siswa Views ===" -ForegroundColor Cyan
Get-ChildItem -Path "resources/views/siswa" -Filter "*.blade.php" -Recurse | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    if ($content -match "route\('siswa\.") {
        if (Update-BladeFile $_.FullName) {
            $updatedCount++
        }
    }
}

# Update guru views
Write-Host "`n=== Updating Guru Views ===" -ForegroundColor Cyan
Get-ChildItem -Path "resources/views/guru" -Filter "*.blade.php" -Recurse | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    if ($content -match "route\('guru\.") {
        if (Update-BladeFile $_.FullName) {
            $updatedCount++
        }
    }
}

Write-Host "`n✓ All files processed!" -ForegroundColor Green
Write-Host "Updated $updatedCount files" -ForegroundColor Green
Write-Host "Backup files created with .bak extension" -ForegroundColor Yellow
Write-Host "`nTo restore backups if needed:" -ForegroundColor Cyan
Write-Host "  Get-ChildItem -Path resources/views -Filter '*.blade.php.bak' -Recurse | ForEach-Object { Move-Item `$_.FullName (`$_.FullName -replace '\.bak$','') -Force }" -ForegroundColor Gray
