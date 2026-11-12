<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Builds a file download response that download managers can handle.
 *
 * A browser is happy with a plain 200 response, but download managers such as
 * IDM, wget or aria2 probe the file first (usually with a HEAD or a
 * "Range: bytes=0-0" request) to learn its size and to decide whether the
 * transfer can be resumed or split into segments. Without a Content-Length
 * header and byte-range support they cannot size the file and give up with
 * an error instead of saving it.
 */
class FileDownloadResponse
{
    /**
     * Create a response for a file that is generated in memory.
     *
     * @param  string  $content  The raw file contents.
     * @param  string  $filename  The name the file should be saved as.
     * @param  string  $mimeType  The content type of the file.
     */
    public static function make(Request $request, string $content, string $filename, string $mimeType): Response
    {
        $size = strlen($content);

        $headers = [
            'Content-Type' => $mimeType,
            'Content-Length' => (string) $size,
            'Accept-Ranges' => 'bytes',
            'Content-Disposition' => self::disposition($filename),
            'Cache-Control' => 'private, no-cache, must-revalidate',
            'X-Content-Type-Options' => 'nosniff',
        ];

        $range = self::requestedRange($request, $size);

        if ($range === 'unsatisfiable') {
            $headers['Content-Range'] = 'bytes */'.$size;
            $headers['Content-Length'] = '0';

            return response('', Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE, $headers);
        }

        if ($range === null) {
            return response($content, Response::HTTP_OK, $headers);
        }

        [$start, $end] = $range;

        $headers['Content-Range'] = sprintf('bytes %d-%d/%d', $start, $end, $size);
        $headers['Content-Length'] = (string) ($end - $start + 1);

        return response(
            substr($content, $start, $end - $start + 1),
            Response::HTTP_PARTIAL_CONTENT,
            $headers
        );
    }

    /**
     * Resolve the byte range the client asked for.
     *
     * Returns null when the whole file should be sent and "unsatisfiable"
     * when the requested range lies outside of the file.
     *
     * @return array{0: int, 1: int}|string|null
     */
    private static function requestedRange(Request $request, int $size)
    {
        $header = $request->headers->get('Range');

        if (! $header || ! preg_match('/^bytes=(\d*)-(\d*)$/', trim($header), $matches)) {
            return null;
        }

        $start = $matches[1] === '' ? null : (int) $matches[1];
        $end = $matches[2] === '' ? null : (int) $matches[2];

        if ($start === null) {
            // Suffix range: the last $end bytes of the file.
            $length = $end ?? 0;

            if ($length <= 0) {
                return null;
            }

            $start = max(0, $size - $length);
            $end = $size - 1;
        } else {
            if ($start >= $size) {
                return 'unsatisfiable';
            }

            $end = $end === null ? $size - 1 : min($end, $size - 1);
        }

        if ($end < $start) {
            return null;
        }

        return [$start, $end];
    }

    /**
     * Build a Content-Disposition header that keeps the file name readable
     * for every client, including non-ASCII names.
     */
    private static function disposition(string $filename): string
    {
        $fallback = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename) ?: 'download';

        return sprintf(
            'attachment; filename="%s"; filename*=UTF-8\'\'%s',
            $fallback,
            rawurlencode($filename)
        );
    }
}
