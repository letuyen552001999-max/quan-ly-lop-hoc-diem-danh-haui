# Chay PowerShell tai thu muc goc du an: powershell -ExecutionPolicy Bypass -File .\tests\kiem_tra_php_windows.ps1
$ErrorActionPreference = 'Stop'
$root = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$php = (Get-Command php -ErrorAction SilentlyContinue).Source
if (-not $php) {
    $xamppPhp = 'C:\xampp\php\php.exe'
    if (Test-Path $xamppPhp) { $php = $xamppPhp }
}
if (-not $php) { Write-Error 'Khong tim thay PHP. Hay cai XAMPP va kiem tra C:\xampp\php\php.exe.'; exit 1 }
$files = Get-ChildItem -Path $root -Recurse -Filter '*.php' -File
$failed = 0
foreach ($file in $files) {
    & $php -l $file.FullName | Out-Host
    if ($LASTEXITCODE -ne 0) { $failed++ }
}
if ($failed -gt 0) { Write-Error "$failed tep PHP bi loi cu phap."; exit 1 }
& $php (Join-Path $root 'tests\test_helpers.php')
if ($LASTEXITCODE -ne 0) { exit 1 }
Write-Host "HOAN THANH: $($files.Count) tep PHP hop le, 24 phep kiem tra ham dat."
Write-Host 'Luu y: chua thay the cho kiem thu voi MySQL/XAMPP.'
