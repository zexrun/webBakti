<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light dark">
<meta name="supported-color-schemes" content="light dark">
<style>
@media only screen and (max-width: 600px) {
.inner-body {
width: 100% !important;
border-radius: 0 !important;
box-shadow: none !important;
}

.footer {
width: 100% !important;
}

.content-cell {
padding: 16px !important;
}

h1 {
font-size: 24px !important;
}

h2 {
font-size: 18px !important;
}

p {
font-size: 14px !important;
}

.header {
padding: 24px 0 !important;
}

.header a {
font-size: 20px !important;
}
}

@media only screen and (max-width: 500px) {
.button {
width: 100% !important;
display: block !important;
margin: 8px 0 !important;
}
}

@media (prefers-color-scheme: dark) {
body {
background-color: #1a1a1a !important;
}

.inner-body {
background-color: #2a2a2a !important;
border-color: #404040 !important;
}

p, .content-cell {
color: #e0e0e0 !important;
}

h1, h2, h3 {
color: #ffffff !important;
}

.subcopy {
border-top-color: #404040 !important;
}

.subcopy p {
color: #a0a0a0 !important;
}

.footer p {
color: #a0a0a0 !important;
}

.panel-content {
background-color: #363636 !important;
border-color: #404040 !important;
color: #d0d0d0 !important;
}

.table tr:nth-child(even) td {
background-color: #323232 !important;
}

.table td {
border-bottom-color: #404040 !important;
}
}
</style>
{!! $head ?? '' !!}
</head>
<body>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center">
<table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
{!! $header ?? '' !!}

<!-- Email Body -->
<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
<table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<!-- Body content -->
<tr>
<td class="content-cell">
{!! Illuminate\Mail\Markdown::parse($slot) !!}

{!! $subcopy ?? '' !!}
</td>
</tr>
</table>
</td>
</tr>

{!! $footer ?? '' !!}
</table>
</td>
</tr>
</table>
</body>
</html>
