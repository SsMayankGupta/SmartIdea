# Auto Git Commit and Push Script
# This script automatically commits and pushes changes every 60 seconds

$repoPath = "C:\laragon\www\ecoconnect"
$intervalSeconds = 300

Write-Host "Starting Auto-Git service..."
Write-Host "Repository: $repoPath"
Write-Host "Interval: $intervalSeconds seconds"
Write-Host "Press Ctrl+C to stop"
Write-Host "----------------------------------------"

while ($true) {
    try {
        Set-Location $repoPath
        
        # Check if there are changes to commit
        $status = git status --porcelain
        
        if ($status) {
            $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
            Write-Host "[$timestamp] Changes detected, committing..."
            
            # Add all changes
            git add .
            
            # Commit with timestamp
            git commit -m "Auto commit: $timestamp"
            
            # Push to remote
            $pushResult = git push origin main 2>&1
            
            if ($LASTEXITCODE -eq 0) {
                Write-Host "[$timestamp] Successfully pushed to origin/main"
            } else {
                Write-Host "[$timestamp] Push failed: $pushResult"
            }
        } else {
            $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
            Write-Host "[$timestamp] No changes to commit"
        }
    }
    catch {
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        Write-Host "[$timestamp] Error: $_"
    }
    
    Start-Sleep -Seconds $intervalSeconds
}
