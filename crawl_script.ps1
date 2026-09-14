$urls = @(
    'http://127.0.0.1:8899/',
    'http://127.0.0.1:8899/about',
    'http://127.0.0.1:8899/team',
    'http://127.0.0.1:8899/pricing',
    'http://127.0.0.1:8899/contact',
    'http://127.0.0.1:8899/blog',
    'http://127.0.0.1:8899/case-studies',
    'http://127.0.0.1:8899/privacy',
    'http://127.0.0.1:8899/terms',
    'http://127.0.0.1:8899/locations',
    'http://127.0.0.1:8899/locations/greater-noida',
    'http://127.0.0.1:8899/locations/noida',
    'http://127.0.0.1:8899/locations/delhi',
    'http://127.0.0.1:8899/locations/gurgaon',
    'http://127.0.0.1:8899/services/web-development',
    'http://127.0.0.1:8899/services/web-security',
    'http://127.0.0.1:8899/services/performance-marketing',
    'http://127.0.0.1:8899/services/content-creation',
    'http://127.0.0.1:8899/services/ecommerce',
    'http://127.0.0.1:8899/services/lead-automation',
    'http://127.0.0.1:8899/landing/security-emergency',
    'http://127.0.0.1:8899/landing/website-audit',
    'http://127.0.0.1:8899/landing/whatsapp-automation',
    'http://127.0.0.1:8899/thank-you',
    'http://127.0.0.1:8899/client-portal',
    'http://127.0.0.1:8899/lottie-test',
    'http://127.0.0.1:8899/sitemap.xml',
    'http://127.0.0.1:8899/robots.txt',
    'http://127.0.0.1:8899/this-page-does-not-exist',
    'http://127.0.0.1:8899/about/',
    'http://127.0.0.1:8899/about.php',
    'http://127.0.0.1:8899/services/ecommerce-support'
)

$results = @()

foreach ($url in $urls) {
    $status = 0
    $content = ""
    $redirect = ""
    try {
        $r = Invoke-WebRequest -Uri $url -UseBasicParsing -MaximumRedirection 0 -ErrorAction Stop
        $content = $r.Content
        $status = $r.StatusCode
    } catch {
        if ($_.Exception.Response) {
            $r = $_.Exception.Response
            $status = [int]$r.StatusCode
            if ($status -ge 300 -and $status -lt 400) {
                $redirect = $r.Headers["Location"]
            } else {
                try {
                    $stream = $r.GetResponseStream()
                    $reader = New-Object System.IO.StreamReader($stream)
                    $content = $reader.ReadToEnd()
                } catch { }
            }
        } elseif ($_.Exception -is [System.InvalidOperationException] -and $_.Exception.Message -match "redirection count") {
            # In powershell 5.1, MaximumRedirection 0 throws this. We can't get the redirect URL easily from the exception.
            # Instead we'll use System.Net.HttpWebRequest to do it properly.
        }
    }

    if ($status -eq 0) {
        try {
            $req = [System.Net.WebRequest]::Create($url)
            $req.AllowAutoRedirect = $false
            $res = $req.GetResponse()
            $status = [int]$res.StatusCode
            if ($status -ge 300 -and $status -lt 400) {
                $redirect = $res.Headers["Location"]
            } else {
                $stream = $res.GetResponseStream()
                $reader = New-Object System.IO.StreamReader($stream)
                $content = $reader.ReadToEnd()
            }
        } catch [System.Net.WebException] {
            if ($_.Exception.Response) {
                $res = $_.Exception.Response
                $status = [int]$res.StatusCode
                if ($status -ge 300 -and $status -lt 400) {
                    $redirect = $res.Headers["Location"]
                } else {
                    $stream = $res.GetResponseStream()
                    $reader = New-Object System.IO.StreamReader($stream)
                    $content = $reader.ReadToEnd()
                }
            } else {
                $status = 500
            }
        } catch {
            $status = 500
        }
    }

    $title = if ($content -match '(?is)<title>(.*?)</title>') { $matches[1].Trim() } else { "" }
    $desc = if ($content -match '(?is)<meta\s+name=["'']description["'']\s+content=["''](.*?)["'']') { $matches[1].Trim() } else { "" }
    $canon = if ($content -match '(?is)<link\s+rel=["'']canonical["'']\s+href=["''](.*?)["'']') { $matches[1].Trim() } else { "" }
    $robots = if ($content -match '(?is)<meta\s+name=["'']robots["'']\s+content=["''](.*?)["'']') { $matches[1].Trim() } else { "" }
    $h1 = if ($content -match '(?is)<h1[^>]*>(.*?)</h1>') { $matches[1] -replace '<[^>]+>','' } else { "" }

    $results += [PSCustomObject]@{
        Url = $url
        Status = $status
        Redirect = $redirect
        Title = $title
        Desc = $desc
        Canon = $canon
        Robots = $robots
        H1 = $h1.Trim()
        Length = $content.Length
    }
}

$results | ConvertTo-Json -Depth 2 | Out-File -FilePath "$env:TEMP\crawl_results.json" -Encoding UTF8
