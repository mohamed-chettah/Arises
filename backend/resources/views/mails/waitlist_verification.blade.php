<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm your email</title>
</head>

<body style="margin:0; padding:0; background:#f2f2f2;">
<!-- Wrapper plein écran gris -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f2f2f2; padding:40px 0;">
    <tr>
        <td align="center">

            <!-- Conteneur central noir -->
            <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                   style="width:600px; max-width:100%; background:#0f0f1a; border-radius:16px; padding:48px 32px; text-align:center; font-family:Segoe UI, Inter, sans-serif;">

                <!-- Logo -->
                <tr>
                    <td style="padding-bottom:24px;">
                        <img src="https://arises.app/logo.png" alt="Arises" width="70" style="display:block; margin:0 auto;">
                    </td>
                </tr>

                <!-- Séparateur -->
                <tr><td><hr style="border:none; border-top:1px solid #9878E8; margin:0 0 32px;"></td></tr>

                <!-- Titre -->
                <tr>
                    <td style="font-size:24px; font-weight:600;color:#ffffff; padding:0 0 16px;">
                        Welcome to Arises
                    </td>
                </tr>

                <!-- Texte principal -->
                <tr>
                    <td style="font-size:15px; line-height:1.5; color:#dddddd; padding:0 0 28px;">
                        You're almost in ! Confirm your email to secure your spot on the waitlist and be one of the first to try Arises.
                    </td>
                </tr>

                <!-- Bouton -->
                <tr>
                    <td style="padding:0 0 32px;">
                        <a href="{{ $url }}" style="
                            background-color: #9878E8;
                            color: #ffffff;
                            text-decoration: none;
                            font-size: 14px;
                            font-weight: 400;
                            padding: 12px;
                            border-radius: 8px;
                            display: inline-block;
                            border-top: 1px solid rgba(164, 128, 242, 0.7);
                            text-align: center;
                            cursor: pointer;
                        ">
                            Confirm my email
                        </a>
                    </td>
                </tr>


                <!-- Lien brut -->
                <tr>
                    <td style="font-size:11px; color:#888888;">
                        <p style="font-size:11px; color:#888888;">Or copy this link into your browser: </p>
                            <br>
                        <span>{{ $url }}</span>
                    </td>
                </tr>

                <!-- Séparateur -->
                <tr><td><hr style="border:none; border-top:1px solid #9878E8; margin:32px 0;"></td></tr>

                <!-- Disclaimer -->
                <tr>
                    <td style="font-size:11px; color:#888888;">
                        You're receiving this email because you joined the Arises waitlist.<br>
                        If this wasn't you, feel free to ignore it.
                    </td>
                </tr>

                <!-- Séparateur bas -->
                <tr><td><hr style="border:none; border-top:1px solid #9878E8; margin:32px 0;"></td></tr>

            </table>
            <!-- Fin conteneur -->

        </td>
    </tr>
</table>
</body>
</html>
