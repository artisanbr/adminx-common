<?php
/**
 * @var \Adminx\Common\Models\Sites\Site $site
 */
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
        body, table, td {
            font-family: Helvetica, Arial, sans-serif !important
        }

        .ExternalClass {
            width: 100%
        }

        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
            line-height: 150%
        }

        a {
            text-decoration: none
        }

        * {
            color: inherit
        }

        a[x-apple-data-detectors], u + #body a, #MessageViewBody a {
            color: inherit;
            text-decoration: none;
            font-size: inherit;
            font-family: inherit;
            font-weight: inherit;
            line-height: inherit
        }

        img {
            -ms-interpolation-mode: bicubic
        }

        table:not([class^=s-]) {
            font-family: Helvetica, Arial, sans-serif;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            border-spacing: 0px;
            border-collapse: collapse
        }

        table:not([class^=s-]) td {
            border-spacing: 0px;
            border-collapse: collapse
        }

        @media screen and (max-width: 600px) {
            .w-full, .w-full > tbody > tr > td {
                width: 100% !important
            }

            *[class*=s-lg-] > tbody > tr > td {
                font-size: 0 !important;
                line-height: 0 !important;
                height: 0 !important
            }

            .s-2 > tbody > tr > td {
                font-size: 8px !important;
                line-height: 8px !important;
                height: 8px !important
            }

            .s-3 > tbody > tr > td {
                font-size: 12px !important;
                line-height: 12px !important;
                height: 12px !important
            }

            .s-5 > tbody > tr > td {
                font-size: 20px !important;
                line-height: 20px !important;
                height: 20px !important
            }

            .s-10 > tbody > tr > td {
                font-size: 40px !important;
                line-height: 40px !important;
                height: 40px !important
            }
        }
    </style>
</head>
<body class="bg-light"
      style="outline: 0; width: 100%; min-width: 100%; height: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 24px; font-weight: normal; font-size: 16px; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000000; margin: 0; padding: 0; border-width: 0;"
      bgcolor="#f7fafc">
<table class="bg-light body" valign="top" role="presentation" border="0" cellpadding="0" cellspacing="0"
       style="outline: 0; width: 100%; min-width: 100%; height: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 24px; font-weight: normal; font-size: 16px; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000000; margin: 0; padding: 0; border-width: 0;"
       bgcolor="#f7fafc">
    <tbody>
    <tr>
        <td valign="top" style="line-height: 24px; font-size: 16px; margin: 0;" align="left" bgcolor="#f7fafc">
            <table class="container" role="presentation" border="0" cellpadding="0" cellspacing="0"
                   style="width: 100%;">
                <tbody>
                    <tr>
                        <td align="center" style="line-height: 24px; font-size: 16px; margin: 0; padding: 0 16px;">
                            <!--[if (gte mso 9)|(IE)]>
                            <table align="center" role="presentation">
                                <tbody>
                                <tr>
                                    <td width="600">
                            <![endif]-->
                            <table align="center" role="presentation" border="0" cellpadding="0" cellspacing="0"
                                   style="width: 100%; max-width: 600px; margin: 0 auto;">
                                <tbody>
                                <tr>
                                    <td style="line-height: 24px; font-size: 16px; margin: 0;" align="left">
                                        <x-common::mail.space size="40"/>
                                        {{--Logo--}}
                                        <div class="text-center" style="" align="center">
                                            <a href="{{ $site->uri }}" target="_blank"
                                               style="color: #0d6efd;">
                                                <img src="{{ $site->uriTo($site->theme->media->logo->url) }}" alt="" style="color: #0d6efd; width: 40%;"/>
                                            </a>
                                        </div>

                                        <x-common::mail.space size="40"/>
                                        <table class="card" role="presentation" border="0" cellpadding="0" cellspacing="0"
                                               style="border-radius: 6px; border-collapse: separate !important; width: 100%; overflow: hidden; border: 1px solid #e2e8f0;"
                                               bgcolor="#ffffff">
                                            <tbody>
                                            <tr>
                                                <td style="line-height: 24px; font-size: 16px; width: 100%; margin: 0;"
                                                    align="left" bgcolor="#ffffff">
                                                    <table class="card-body" role="presentation" border="0" cellpadding="0"
                                                           cellspacing="0" style="width: 100%;">
                                                        <tbody>
                                                        <tr>
                                                            <td style="line-height: 24px; font-size: 16px; width: 100%; margin: 0; padding: 20px;"
                                                                align="left">
                                                                {{--Titulo--}}
                                                                <h1 class="h3"
                                                                    style="padding-top: 0; padding-bottom: 0; font-weight: 500; vertical-align: baseline; font-size: 24px; line-height: 30px; margin: 0;"
                                                                    align="left">@yield('subject')</h1>

                                                                @hasSection('description')
                                                                <x-common::mail.space size="15"/>

                                                                <h5 class="text-teal-700"
                                                                    style="color: #13795b; padding-top: 0; padding-bottom: 0; font-weight: 500; vertical-align: baseline; font-size: 16px; line-height: 22px; margin: 0;"
                                                                    align="left">@yield('description')</h5>
                                                                @endif

                                                                <x-common::mail.space/>
                                                                <x-common::mail.separator/>
                                                                <x-common::mail.space/>

                                                                <div class="space-y-3">
                                                                    @yield('content')
                                                                </div>

                                                                <x-common::mail.space/>
                                                                <x-common::mail.separator/>
                                                                <x-common::mail.space/>

                                                                <div class="text-center" style="" align="center">
                                                                    <table class="btn btn-primary" role="presentation"
                                                                           border="0" cellpadding="0" cellspacing="0"
                                                                           style="border-radius: 6px; border-collapse: separate !important;">
                                                                        <tbody>
                                                                        <tr>
                                                                            <td style="line-height: 24px; font-size: 16px; border-radius: 6px; margin: 0;"
                                                                                align="center" bgcolor="#0d6efd">
                                                                                {{--<a href="@yield('action_uri', appUrl())"
                                                                                   target="_blank"
                                                                                   style="color: #ffffff; font-size: 16px; font-family: Helvetica, Arial, sans-serif; text-decoration: none; border-radius: 6px; line-height: 20px; display: block; font-weight: normal; white-space: nowrap; background-color: #0d6efd; padding: 8px 12px; border: 1px solid #0d6efd;">@yield('action_text', 'Acessar Painel')</a>--}}
                                                                            </td>
                                                                        </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <x-common::mail.space size="40"/>
                                        <div class="text-center text-gray-700" style="color: #4a5568;" align="center">
                                            <small>{{ date('Y') }} &copy; Desenvolvido por <a href="https://tanda.com.br"
                                                                                              target="_blank"
                                                                                              style="color: #0d6efd;">Tanda</a>.</small>
                                        </div>
                                        <x-common::mail.space size="40"/>

                                        <x-common::mail.space />
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <!--[if (gte mso 9)|(IE)]>
                            </td>
                            </tr>
                            </tbody>
                            </table>
                            <![endif]-->
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
