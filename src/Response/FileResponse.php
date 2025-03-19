<?php

namespace PhpFramework\Response;

use PhpFramework\Response\Interface\IResponse;

class FileResponse implements IResponse
{
    const DefaultContentType = 'application/octet-stream';
    const ChunkSize = 2 * (1024 * 1024);

    public int $FileSize = 0;

    public function __construct(
        public string $FilePath,
        public string $FileName,
        public string $ContentType = self::DefaultContentType
    ) {
        $this->FileSize = filesize($FilePath);
    }

    public function Response(): ?string
    {
        set_time_limit(0);

        if (isset($_SERVER['HTTP_RANGE'])) {
            [$size_unit, $range_orig] = explode('=', $_SERVER['HTTP_RANGE'], 2);
            $range = explode(',', $range_orig, 2)[0];
            [$range_start, $range_end] = explode('-', $range, 2);
            $range_start = (int) $range_start;
            $range_end = $range_end == '' ? $this->FileSize - 1 : (int) $range_end;

            if ($size_unit != 'bytes' || $range_start >= $range_end || $range_end > $this->FileSize - 1 || $range_start < 0 || $range_end < 0) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header('Content-Range: bytes */' . $this->FileSize);

                return null;
            }

            header('HTTP/1.1 206 Partial Content');
            header('Accept-Ranges: bytes');
            header('Content-Type: ' . $this->ContentType);
            header('Content-Length: ' . ($range_end - $range_start + 1));
            header('Content-Disposition: attachment;filename=' . $this->FileName);
            header('Content-Range: bytes ' . $range_start . '-' . $range_end . '/' . $this->FileSize);

            $handle = fopen($this->FilePath, 'rb');
            fseek($handle, $range_start);

            while (!feof($handle) && $range_start < $range_end && (connection_status() === CONNECTION_NORMAL)) {
                echo fread($handle, self::ChunkSize);
                ob_flush();
                flush();
                $range_start += self::ChunkSize;
            }

            fclose($handle);

            return null;
        }

        header('Content-Disposition: attachment;filename=' . $this->FileName);
        header('Content-Type: ' . $this->ContentType);
        header('Accept-Ranges: bytes');
        header('Pragma: public');
        header('Expires: -1');
        header('Cache-Control: no-cache');
        header('Cache-Control: public, must-revalidate, post-check=0, pre-check=0');
        header('Content-Length: ' . $this->FileSize);

        if ($this->FileSize > self::ChunkSize) {
            $handle = fopen($this->FilePath, 'rb');

            while (!feof($handle) && (connection_status() === CONNECTION_NORMAL)) {
                echo fread($handle, self::ChunkSize);
                ob_flush();
                flush();
            }

            fclose($handle);
        } else {
            readfile($this->FilePath);

            ob_clean();
            flush();
        }

        return null;
    }
}
