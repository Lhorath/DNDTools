param(
    [string]$BaseUrl = "http://localhost",
    [string]$LoginEmail = "",
    [string]$LoginPassword = "",
    [string]$ReportJsonPath = ".\smoke-test-report.json",
    [string]$ReportJunitPath = ".\smoke-test-report.xml"
)

$ErrorActionPreference = "Stop"
$BaseUrl = $BaseUrl.TrimEnd("/")

$results = @()

function Add-Result {
    param(
        [string]$Name,
        [ValidateSet("passed", "failed", "skipped")][string]$Status,
        [string]$Details = ""
    )
    $script:results += [pscustomobject]@{
        name = $Name
        status = $Status
        details = $Details
    }
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

function Write-Reports {
    param(
        [array]$TestResults,
        [string]$JsonPath,
        [string]$JunitPath
    )

    $passed = ($TestResults | Where-Object { $_.status -eq "passed" }).Count
    $failed = ($TestResults | Where-Object { $_.status -eq "failed" }).Count
    $skipped = ($TestResults | Where-Object { $_.status -eq "skipped" }).Count
    $total = $TestResults.Count

    $jsonPayload = [pscustomobject]@{
        generated_at = (Get-Date).ToString("o")
        base_url = $BaseUrl
        summary = [pscustomobject]@{
            total = $total
            passed = $passed
            failed = $failed
            skipped = $skipped
        }
        tests = $TestResults
    }
    $jsonPayload | ConvertTo-Json -Depth 8 | Out-File -FilePath $JsonPath -Encoding utf8

    $xml = New-Object System.Xml.XmlDocument
    $declaration = $xml.CreateXmlDeclaration("1.0", "UTF-8", $null)
    [void]$xml.AppendChild($declaration)

    $suite = $xml.CreateElement("testsuite")
    [void]$suite.SetAttribute("name", "smoke-test-ci")
    [void]$suite.SetAttribute("tests", [string]$total)
    [void]$suite.SetAttribute("failures", [string]$failed)
    [void]$suite.SetAttribute("skipped", [string]$skipped)
    [void]$suite.SetAttribute("errors", "0")
    [void]$suite.SetAttribute("timestamp", (Get-Date).ToString("s"))
    [void]$xml.AppendChild($suite)

    foreach ($test in $TestResults) {
        $case = $xml.CreateElement("testcase")
        [void]$case.SetAttribute("classname", "smoke-test-ci")
        [void]$case.SetAttribute("name", $test.name)

        if ($test.status -eq "failed") {
            $failure = $xml.CreateElement("failure")
            [void]$failure.SetAttribute("message", if ([string]::IsNullOrWhiteSpace($test.details)) { "failed" } else { $test.details })
            $failure.InnerText = $test.details
            [void]$case.AppendChild($failure)
        } elseif ($test.status -eq "skipped") {
            $skip = $xml.CreateElement("skipped")
            [void]$skip.SetAttribute("message", if ([string]::IsNullOrWhiteSpace($test.details)) { "skipped" } else { $test.details })
            [void]$case.AppendChild($skip)
        }

        [void]$suite.AppendChild($case)
    }

    $xml.Save($JunitPath)
}

Write-Host "Running CI smoke tests against $BaseUrl" -ForegroundColor Cyan
$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession

# 1) Bootstrap session + CSRF token
$bootstrap = Invoke-WebRequestWithStatus {
    Invoke-WebRequest -Uri "$BaseUrl/login" -Method Get -WebSession $session
}
if ($bootstrap.StatusCode -eq 200) {
    Add-Result -Name "login page loads" -Status "passed"
} else {
    Add-Result -Name "login page loads" -Status "failed" -Details "Expected status 200, got $($bootstrap.StatusCode)."
}

$csrfToken = Get-CsrfTokenFromHtml $bootstrap.Content
if (-not [string]::IsNullOrWhiteSpace($csrfToken)) {
    Add-Result -Name "csrf token exposed in html" -Status "passed"
} else {
    Add-Result -Name "csrf token exposed in html" -Status "failed" -Details "Missing csrf-token meta content."
}

# 2) Unknown route should not leak warnings/fatal errors
$unknownRoute = Invoke-WebRequestWithStatus {
    Invoke-WebRequest -Uri "$BaseUrl/this-route-should-not-exist-smoke-test" -Method Get -WebSession $session
}
$unknownContent = $unknownRoute.Content
if ($unknownContent -match "Warning:" -or $unknownContent -match "Fatal error") {
    Add-Result -Name "unknown route has clean output" -Status "failed" -Details "Response leaked PHP warning/fatal error."
} else {
    Add-Result -Name "unknown route has clean output" -Status "passed"
}

# 3) Search pagination clamping checks
$searchZero = Invoke-WebRequestWithStatus {
    Invoke-WebRequest -Uri "$BaseUrl/includes/core/search.php?category=spells&page=0&limit=0" -Method Get -WebSession $session
}
if ($searchZero.StatusCode -eq 200) {
    try {
        $data = $searchZero.Content | ConvertFrom-Json
        if ($data.pagination.current_page -ge 1 -and $data.pagination.limit -ge 1) {
            Add-Result -Name "search clamps minimum page and limit" -Status "passed"
        } else {
            Add-Result -Name "search clamps minimum page and limit" -Status "failed" -Details "current_page=$($data.pagination.current_page), limit=$($data.pagination.limit)"
        }
    } catch {
        Add-Result -Name "search clamps minimum page and limit" -Status "failed" -Details "Response was not valid JSON."
    }
} else {
    Add-Result -Name "search clamps minimum page and limit" -Status "failed" -Details "Expected status 200, got $($searchZero.StatusCode)."
}

$searchHigh = Invoke-WebRequestWithStatus {
    Invoke-WebRequest -Uri "$BaseUrl/includes/core/search.php?category=spells&page=1&limit=999" -Method Get -WebSession $session
}
if ($searchHigh.StatusCode -eq 200) {
    try {
        $data = $searchHigh.Content | ConvertFrom-Json
        if ($data.pagination.limit -le 100) {
            Add-Result -Name "search clamps maximum limit" -Status "passed"
        } else {
            Add-Result -Name "search clamps maximum limit" -Status "failed" -Details "limit=$($data.pagination.limit)"
        }
    } catch {
        Add-Result -Name "search clamps maximum limit" -Status "failed" -Details "Response was not valid JSON."
    }
} else {
    Add-Result -Name "search clamps maximum limit" -Status "failed" -Details "Expected status 200, got $($searchHigh.StatusCode)."
}

# 4) Auth endpoints should reject missing/invalid CSRF
$invalidLoginPayload = @{ email = "nobody@example.invalid"; password = "wrongpass" } | ConvertTo-Json

$loginNoToken = Invoke-WebRequestWithStatus {
    Invoke-WebRequest -Uri "$BaseUrl/includes/user/login.php" -Method Post -WebSession $session -ContentType "application/json" -Body $invalidLoginPayload
}
if ($loginNoToken.StatusCode -eq 403) {
    Add-Result -Name "login rejects missing csrf token" -Status "passed"
} else {
    Add-Result -Name "login rejects missing csrf token" -Status "failed" -Details "Expected status 403, got $($loginNoToken.StatusCode)."
}

$loginBadToken = Invoke-WebRequestWithStatus {
    Invoke-WebRequest -Uri "$BaseUrl/includes/user/login.php" -Method Post -WebSession $session -ContentType "application/json" -Headers @{ "X-CSRF-Token" = "invalid-token" } -Body $invalidLoginPayload
}
if ($loginBadToken.StatusCode -eq 403) {
    Add-Result -Name "login rejects invalid csrf token" -Status "passed"
} else {
    Add-Result -Name "login rejects invalid csrf token" -Status "failed" -Details "Expected status 403, got $($loginBadToken.StatusCode)."
}

# 5) PDF endpoint should reject missing CSRF
$pdfNoToken = Invoke-WebRequestWithStatus {
    Invoke-WebRequest -Uri "$BaseUrl/includes/core/fill-pdf.php" -Method Post -WebSession $session -ContentType "application/json" -Body "{}"
}
if ($pdfNoToken.StatusCode -eq 403) {
    Add-Result -Name "pdf endpoint rejects missing csrf token" -Status "passed"
} else {
    Add-Result -Name "pdf endpoint rejects missing csrf token" -Status "failed" -Details "Expected status 403, got $($pdfNoToken.StatusCode)."
}

# 6) Optional positive login test
if (-not [string]::IsNullOrWhiteSpace($LoginEmail) -and -not [string]::IsNullOrWhiteSpace($LoginPassword)) {
    $loginPayload = @{ email = $LoginEmail; password = $LoginPassword } | ConvertTo-Json
    $loginWithToken = Invoke-WebRequestWithStatus {
        Invoke-WebRequest -Uri "$BaseUrl/includes/user/login.php" -Method Post -WebSession $session -ContentType "application/json" -Headers @{ "X-CSRF-Token" = $csrfToken } -Body $loginPayload
    }
    if ($loginWithToken.StatusCode -eq 200) {
        Add-Result -Name "positive login with valid csrf token" -Status "passed"
    } else {
        Add-Result -Name "positive login with valid csrf token" -Status "failed" -Details "Expected status 200, got $($loginWithToken.StatusCode)."
    }
} else {
    Add-Result -Name "positive login with valid csrf token" -Status "skipped" -Details "Provide -LoginEmail and -LoginPassword."
}

Write-Reports -TestResults $results -JsonPath $ReportJsonPath -JunitPath $ReportJunitPath

$passed = ($results | Where-Object { $_.status -eq "passed" }).Count
$failed = ($results | Where-Object { $_.status -eq "failed" }).Count
$skipped = ($results | Where-Object { $_.status -eq "skipped" }).Count

Write-Host ""
Write-Host "CI smoke summary: $passed passed, $failed failed, $skipped skipped." -ForegroundColor Cyan
Write-Host "JSON report:  $ReportJsonPath" -ForegroundColor DarkCyan
Write-Host "JUnit report: $ReportJunitPath" -ForegroundColor DarkCyan

if ($failed -gt 0) {
    exit 1
}
exit 0
