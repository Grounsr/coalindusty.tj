<?php
/**
 * Mailer — minimal SMTP client using fsockopen (no Composer dependencies).
 * Supports SSL (port 465) and STARTTLS (port 587). HTML emails with proper headers.
 *
 * Falls back to PHP mail() if SMTP is disabled in config.
 */
declare(strict_types=1);

class Mailer
{
    public static function send(string $to, string $subject, string $htmlBody, array $extraHeaders = []): bool
    {
        global $CONFIG;
        $cfg = $CONFIG['mail'] ?? [];
        if (empty($cfg['enabled'])) {
            error_log('[Mailer] disabled — skipping send to ' . $to);
            return false;
        }

        $from      = $cfg['from_email'] ?? 'no-reply@localhost';
        $fromName  = $cfg['from_name']  ?? 'Coal Industry Forum';
        $replyTo   = $cfg['info_email'] ?? $from;

        $boundary  = '=_coalmail_' . bin2hex(random_bytes(8));
        $headers   = [
            'From'         => '=?UTF-8?B?' . base64_encode($fromName) . '?= <' . $from . '>',
            'Reply-To'     => $replyTo,
            'MIME-Version' => '1.0',
            'Content-Type' => 'multipart/alternative; boundary="' . $boundary . '"',
            'X-Mailer'     => 'CoalForumMailer/1.0',
            'Date'         => date('r'),
            'Message-ID'   => '<' . bin2hex(random_bytes(12)) . '@' . ($_SERVER['SERVER_NAME'] ?? 'coalindustry.tj') . '>',
        ];
        foreach ($extraHeaders as $k => $v) $headers[$k] = $v;

        $plain = trim(preg_replace('/\s+/', ' ', strip_tags($htmlBody)));
        $body  = "--$boundary\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $plain . "\r\n\r\n";
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $htmlBody . "\r\n";
        $body .= "--$boundary--\r\n";

        $subjectEncoded = '=?UTF-8?B?' . base64_encode($subject) . '?=';

        try {
            return self::sendSmtp($cfg, $to, $subjectEncoded, $body, $headers);
        } catch (Throwable $e) {
            error_log('[Mailer] SMTP failed: ' . $e->getMessage());
            return self::sendMailFunction($to, $subjectEncoded, $body, $headers);
        }
    }

    private static function sendSmtp(array $cfg, string $to, string $subject, string $body, array $headers): bool
    {
        $host = $cfg['host'];
        $port = (int)$cfg['port'];
        $enc  = strtolower($cfg['encryption'] ?? '');
        $user = $cfg['username'];
        $pass = $cfg['password'];
        $from = $cfg['from_email'];

        $remote = ($enc === 'ssl') ? 'ssl://' . $host : $host;
        $fp = @stream_socket_client($remote . ':' . $port, $errno, $errstr, 15, STREAM_CLIENT_CONNECT);
        if (!$fp) throw new RuntimeException("Connect: $errstr ($errno)");
        stream_set_timeout($fp, 15);

        $read = function() use ($fp) {
            $resp = '';
            while ($line = fgets($fp, 1024)) {
                $resp .= $line;
                if (isset($line[3]) && $line[3] === ' ') break;
            }
            return $resp;
        };
        $send = function(string $cmd) use ($fp) { fwrite($fp, $cmd . "\r\n"); };
        $expect = function(int $code) use (&$read) {
            $r = $read();
            if ((int)substr($r, 0, 3) !== $code) throw new RuntimeException("SMTP expected $code, got: " . trim($r));
        };

        $expect(220);
        $send('EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'coalindustry.tj')); $expect(250);

        if ($enc === 'tls') {
            $send('STARTTLS'); $expect(220);
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new RuntimeException('TLS negotiation failed');
            }
            $send('EHLO ' . ($_SERVER['SERVER_NAME'] ?? 'coalindustry.tj')); $expect(250);
        }

        if ($user) {
            $send('AUTH LOGIN'); $expect(334);
            $send(base64_encode($user)); $expect(334);
            $send(base64_encode($pass)); $expect(235);
        }

        $send('MAIL FROM:<' . $from . '>'); $expect(250);
        $send('RCPT TO:<' . $to . '>');     $expect(250);
        $send('DATA');                       $expect(354);

        $rawHeaders = '';
        foreach ($headers as $k => $v) $rawHeaders .= $k . ': ' . $v . "\r\n";
        $rawHeaders .= 'To: ' . $to . "\r\n";
        $rawHeaders .= 'Subject: ' . $subject . "\r\n";

        // Dot-stuff
        $data = preg_replace('/^\./m', '..', $rawHeaders . "\r\n" . $body);
        $send($data . "\r\n.");
        $expect(250);

        $send('QUIT');
        fclose($fp);
        return true;
    }

    private static function sendMailFunction(string $to, string $subject, string $body, array $headers): bool
    {
        $hdrStr = '';
        foreach ($headers as $k => $v) $hdrStr .= "$k: $v\r\n";
        return @mail($to, $subject, $body, $hdrStr);
    }
}
