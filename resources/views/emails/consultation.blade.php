<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Новая заявка на консультацию</title>
</head>
<body style="margin:0; padding:0; background-color:#f2f4f7; font-family:Arial, 'Segoe UI', sans-serif; color:#1a1a1a;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f2f4f7; padding:32px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:14px; overflow:hidden; box-shadow:0 6px 24px rgba(16,42,80,0.08);">

          <!-- Header -->
          <tr>
            <td style="background:linear-gradient(135deg,#1C508F 0%,#003F8D 100%); padding:32px 40px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td>
                    <div style="font-size:13px; letter-spacing:2px; text-transform:uppercase; color:#a9c6ef; font-weight:600;">Almep Trading</div>
                    <div style="font-size:23px; font-weight:700; color:#ffffff; margin-top:6px;">Новая заявка на консультацию</div>
                  </td>
                  <td align="right" style="vertical-align:top;">
                    <span style="display:inline-block; background-color:rgba(255,255,255,0.16); color:#ffffff; font-size:12px; font-weight:600; padding:7px 14px; border-radius:20px;">Заявка&nbsp;#{{ $item->id }}</span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Intro -->
          <tr>
            <td style="padding:32px 40px 8px;">
              <p style="margin:0; font-size:15px; line-height:1.6; color:#4a5568;">
                Через форму на сайте оставлена новая заявка на бесплатную консультацию. Свяжитесь с клиентом при первой возможности.
              </p>
            </td>
          </tr>

          <!-- Details card -->
          <tr>
            <td style="padding:16px 40px 8px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f7f9fc; border:1px solid #e4e9f2; border-radius:10px;">
                <tr>
                  <td style="padding:18px 22px; border-bottom:1px solid #e9edf4;">
                    <div style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#8a97a8; font-weight:600;">Имя</div>
                    <div style="font-size:17px; color:#1a1a1a; font-weight:600; margin-top:4px;">{{ $item->name }}</div>
                  </td>
                </tr>
                <tr>
                  <td style="padding:18px 22px; border-bottom:1px solid #e9edf4;">
                    <div style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#8a97a8; font-weight:600;">Телефон</div>
                    <div style="font-size:17px; margin-top:4px;">
                      <a href="tel:{{ preg_replace('/[^0-9+]/', '', $item->phone) }}" style="color:#1C508F; text-decoration:none; font-weight:600;">{{ $item->phone }}</a>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td style="padding:18px 22px;">
                    <div style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:#8a97a8; font-weight:600;">E-mail</div>
                    <div style="font-size:17px; margin-top:4px;">
                      <a href="mailto:{{ $item->email }}" style="color:#1C508F; text-decoration:none; font-weight:600;">{{ $item->email }}</a>
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- CTA -->
          <tr>
            <td style="padding:20px 40px 8px;">
              <table role="presentation" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="border-radius:6px; background-color:#1C508F;">
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $item->phone) }}" style="display:inline-block; padding:13px 28px; font-size:15px; font-weight:600; color:#ffffff; text-decoration:none;">Позвонить клиенту</a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Meta -->
          <tr>
            <td style="padding:16px 40px 28px;">
              <p style="margin:0; font-size:13px; color:#98a2b3;">
                Получено: {{ optional($item->created_at)->format('d.m.Y H:i') ?? now()->format('d.m.Y H:i') }}
              </p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background-color:#0f1e33; padding:22px 40px;">
              <p style="margin:0; font-size:13px; color:#8fa2bd; line-height:1.6;">
                Это автоматическое уведомление с сайта <strong style="color:#ffffff;">Almep Trading</strong>.<br>
                Чтобы ответить клиенту, просто нажмите «Ответить» — письмо уйдёт на его e-mail.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
