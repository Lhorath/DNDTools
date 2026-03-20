param(
    [string]$BaseUrl = "http://localhost",
    [string]$LoginEmail = "",
    [string]$LoginPassword = ""
)

$ErrorActionPreference = "Stop"
$BaseUrl = $BaseUrl.TrimEnd("/")

$total = 0
$passed = 0
$failed = 0
$skipped = 0

function Write-Pass([string]$Message) {
    $script:total++
    $script:passed++
    Write-Host "[PASS] $Message" -ForegroundColor Green
}

function Write-Fail([string]$Message) {
    $script:total++
    $script:failed++
    Write-Host "[FAIL] $Message" -ForegroundColor Red
}

function Write-Skip([string]$Message) {
    $script:skipped++
    Write-Host "[SKIP] $Message" -ForegroundColor Yellow
}

function Get-StatusCodeFromException($Exception) {
    if ($null -eq $Exception -or $null -eq $Exception.Response) {
        return -1
    }
    try {
        return [int]$Exception.Response.StatusCode
    } catch {
        return -1
    }
}

function Invoke-WebRequestWithStatus {
    param(
        [Parameter(Mandatory = $true)][scriptblock]$RequestScript
    )
    try {
        $response = & $RequestScript
        return @{
            StatusCode = [int]$response.StatusCode
            Content = $response.Content
            IsError = $false
        }
    } catch {
        $statusCode = Get-StatusCodeFromException $_.Exception
        $content = ""
        try {
            if ($_.Exception.Response -and $_.Exception.Response.GetResponseStream()) {
                $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
                $content = $reader.ReadToEnd()
                $reader.Dispose()
            }
        } catch {
            $content = ""
        }
        return @{
            StatusCode = $statusCode
            Content = $content
            IsError = $true
        }
    }
}

function Get-CsrfTokenFromHtml([string]$Html) {
    if ([string]::IsNullOrEmpty($Html)) {
        return ""
    }
    $match = [regex]::Match($Html, '<meta\s+name=["'']csrf-token["'']\s+content=["'']([^"'']*)["'']', 'IgnoreCase')
    if ($match.Success) {
        return $match.Groups[1].Value
    }
    return ""
}

Write-Host "Running smoke tests against $BaseUrl" -ForegroundColor Cyan
$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession

# 1) Bootstrap session + CSRF token
$bootstrap = Invoke-WebRequestWithStatus {
    Invoke-WebRequest -Uri "$BaseUrl/login" -Method Get -WebSession $session
}
if ($bootstrap.StatusCode -eq 200) {
    Write-Pass "Login page loads (session bootstrap)."
} else {
    Write-Fail "Login page bootstrap failed (status: $($bootstrap.StatusCode))."
}

$csrfToken = Get-CsrfTokenFromHtml $bootstrap.Content
if (-not [string]::IsNullOrWhiteSpace($csrfToken)) {
    Write-Pass "CSRF token found in page meta."
} else {
    Write-Fail "CSRF token missing in page meta."
}

# 2) Unknown route should not leak warnings/fatal errors
$unknownRoute = Invoke-WebRequestWithStatus {
    Invoke-WebRequest -Uri "$BaseUrl/this-route-should-not-exist-smoke-test" -Method Get -WebSession $session
}
$unknownContent = $unknownRoute.Content
if ($unknownContent -match "Warning:" -or $unknownContent -match "Fatal error") {
    Write-Fail "Unknown route response leaked PHP warnings/errors."
} else {
    Write-Pass "Unknown route response contains no PHP warnings/errors."
}

# 3) Search pagination clamping checks
$searchZero = Invoke-WebRequestWithStatus {
    Invoke-WebRequest -Uri "$BaseUrl/includes/core/search.php?category=spells&page=0&limit=0" -Method Get -WebSession $session
}
if ($searchZero.StatusCode -eq 200) {
    try {
        $data = $searchZero.Content | ConvertFrom-Json
        if ($data.pagination.current_page -ge 1 -and $data.pagination.limit -ge 1) {
            Write-Pass "Search clamps page/limit minimum values."
        } else {
            Write-Fail "Search minimum clamp failed (page=$($data.pagination.current_page), limit=$($data.pagination.limit))."
        }
    } catch {
        Write-Fail "Search minimum clamp response was not valid JSON."
    }
} else {
    Write-Fail "Search minimum clamp request failed (status: $($searchZero.StatusCode))."
}

$searchHigh = Invoke-WebRequestWithStatus {
    Invoke-WebRequest -Uri "$BaseUrl/includes/core/search.php?category=spells&page=1&limit=999" -Method Get -WebSession $session
}
if ($searchHigh.StatusCode -eq 200) {
    try {
        $data = $searchHigh.Content | ConvertFrom-Json
        if ($data.pagination.limit -le 100) {
            Write-Pass "Search clamps limit upper bound."
        } else {
            Write-Fail "Search upper clamp failed (limit=$($data.pagination.limit))."
        }
    } catch {
        Write-Fail "Search upper clamp response was not valid JSON."
    }
} else {
    Write-Fail "Search upper clamp request failed (status: $($searchHigh.StatusCode))."
}

# 4) Auth endpoints should reject missing/invalid CSRF
$invalidLoginPayload = @{ email = "nobody@example.invalid"; password = "wrongpass" } | ConvertTo-Json

$loginNoToken = Invoke-WebRequestWithStatus {
    Invoke-WebRequest -Uri "$BaseUrl/includes/user/login.php" -Method Post -WebSession $session -ContentType "application/json" -Body $invalidLoginPayload
}
if ($loginNoToken.StatusCode -eq 403) {
    Write-Pass "Login rejects requests without CSRF token."
} else {
    Write-Fail "Login without CSRF expected 403, got $($loginNoToken.StatusCode)."
}

$loginBadToken = Invoke-WebRequestWithStatus {
    Invoke-WebRequest -Uri "$BaseUrl/includes/user/login.php" -Method Post -WebSession $session -ContentType "application/json" -Headers @{ "X-CSRF-Token" = "invalid-token" } -Body $invalidLoginPayload
}
if ($loginBadToken.StatusCode -eq 403) {
    Write-Pass "Login rejects requests with invalid CSRF token."
} else {
    Write-Fail "Login with invalid CSRF expected 403, got $($loginBadToken.StatusCode)."
}

# 5) PDF endpoint should reject missing CSRF
$pdfNoToken = Invoke-WebRequestWithStatus {
    Invoke-WebRequest -Uri "$BaseUrl/includes/core/fill-pdf.php" -Method Post -WebSession $session -ContentType "application/json" -Body "{}"
}
if ($pdfNoToken.StatusCode -eq 403) {
    Write-Pass "PDF endpoint rejects requests without CSRF token."
} else {
    Write-Fail "PDF endpoint without CSRF expected 403, got $($pdfNoToken.StatusCode)."
}

# 6) Optional positive login test (only if credentials provided)
if (-not [string]::IsNullOrWhiteSpace($LoginEmail) -and -not [string]::IsNullOrWhiteSpace($LoginPassword)) {
    $loginPayload = @{ email = $LoginEmail; password = $LoginPassword } | ConvertTo-Json
    $loginWithToken = Invoke-WebRequestWithStatus {
        Invoke-WebRequest -Uri "$BaseUrl/includes/user/login.php" -Method Post -WebSession $session -ContentType "application/json" -Headers @{ "X-CSRF-Token" = $csrfToken } -Body $loginPayload
    }
    if ($loginWithToken.StatusCode -eq 200) {
        Write-Pass "Positive login with valid CSRF token succeeded."
    } else {
        Write-Fail "Positive login expected 200, got $($loginWithToken.StatusCode)."
    }
} else {
    Write-Skip "Positive login test skipped (provide -LoginEmail and -LoginPassword)."
}

Write-Host ""
Write-Host "Smoke test summary: $passed passed, $failed failed, $skipped skipped." -ForegroundColor Cyan
if ($failed -gt 0) {
    exit 1
}
exit 0
