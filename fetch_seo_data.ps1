$urls = @(
    "/", "/about", "/team", "/pricing", "/contact", "/blog", "/case-studies", "/privacy", "/terms",
    "/locations", "/locations/greater-noida", "/locations/noida", "/locations/delhi", "/locations/gurgaon",
    "/services/web-development", "/services/web-security", "/services/performance-marketing",
    "/services/content-creation", "/services/ecommerce", "/services/lead-automation",
    "/landing/security-emergency", "/landing/website-audit", "/landing/whatsapp-automation",
    "/thank-you", "/client-portal"
)

$base = "http://127.0.0.1:8899"

$results = @()

foreach ($url in $urls) {
    $fullUrl = $base + $url
    try {
        $response = Invoke-WebRequest -Uri $fullUrl -UseBasicParsing
        $html = $response.Content
        
        $canonical = $null
        if ($html -match '<link\s+rel="canonical"\s+href="([^"]+)"') {
            $canonical = $matches[1]
        }

        $schemas = @()
        $pattern = '(?s)<script type="application/ld\+json">(.*?)</script>'
        $matchesList = [regex]::Matches($html, $pattern)
        foreach ($m in $matchesList) {
            $schemas += $m.Groups[1].Value
        }

        $results += [PSCustomObject]@{
            Path = $url
            Status = $response.StatusCode
            Canonical = $canonical
            Schemas = $schemas -join " | "
        }
    } catch {
        $results += [PSCustomObject]@{
            Path = $url
            Status = "Error: $_"
            Canonical = $null
            Schemas = $null
        }
    }
}

$results | ConvertTo-Json -Depth 10 | Out-File -FilePath C:\Users\xshan\Desktop\rafly\scratch_audit.json
