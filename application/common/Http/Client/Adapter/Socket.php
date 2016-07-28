<?php

class Common_Http_Client_Adapter_Socket extends Zend_Http_Client_Adapter_Socket
{
    /**
     * statusコードが204、304でもボディを返すように修正したread
     * 
     * @return string
     */
    public function read()
    {
         // First, read headers only
        $response = '';
        $gotStatus = false;
        $stream = !empty($this->config['stream']);

        while (($line = @fgets($this->socket)) !== false) {
            $gotStatus = $gotStatus || (strpos($line, 'HTTP') !== false);
            if ($gotStatus) {
                $response .= $line;
                if (rtrim($line) === '') break;
            }
        }

        $this->_checkSocketReadTimeout();

        $statusCode = Zend_Http_Response::extractCode($response);

        // Handle 100 and 101 responses internally by restarting the read again
        if ($statusCode == 100 || $statusCode == 101) return $this->read();

        // Check headers to see what kind of connection / transfer encoding we have
        $headers = Zend_Http_Response::extractHeaders($response);

        /**
         * 204と304でボディを返すように修正
         */
        if (
//                $statusCode == 304 || $statusCode == 204 ||
            $this->method == Zend_Http_Client::HEAD) {

            // Close the connection if requested to do so by the server
            if (isset($headers['connection']) && $headers['connection'] == 'close') {
                $this->close();
            }
            return $response;
        }

        // If we got a 'transfer-encoding: chunked' header
        if (isset($headers['transfer-encoding'])) {

            if (strtolower($headers['transfer-encoding']) == 'chunked') {

                do {
                    $line  = @fgets($this->socket);
                    $this->_checkSocketReadTimeout();

                    $chunk = $line;

                    // Figure out the next chunk size
                    $chunksize = trim($line);
                    if (! ctype_xdigit($chunksize)) {
                        $this->close();
                        require_once 'Zend/Http/Client/Adapter/Exception.php';
                        throw new Zend_Http_Client_Adapter_Exception('Invalid chunk size "' .
                            $chunksize . '" unable to read chunked body');
                    }

                    // Convert the hexadecimal value to plain integer
                    $chunksize = hexdec($chunksize);

                    // Read next chunk
                    $read_to = ftell($this->socket) + $chunksize;

                    do {
                        $current_pos = ftell($this->socket);
                        if ($current_pos >= $read_to) break;

                        if($this->out_stream) {
                            if(stream_copy_to_stream($this->socket, $this->out_stream, $read_to - $current_pos) == 0) {
                              $this->_checkSocketReadTimeout();
                              break;
                             }
                        } else {
                            $line = @fread($this->socket, $read_to - $current_pos);
                            if ($line === false || strlen($line) === 0) {
                                $this->_checkSocketReadTimeout();
                                break;
                            }
                                    $chunk .= $line;
                        }
                    } while (! feof($this->socket));

                    $chunk .= @fgets($this->socket);
                    $this->_checkSocketReadTimeout();

                    if(!$this->out_stream) {
                        $response .= $chunk;
                    }
                } while ($chunksize > 0);
            } else {
                $this->close();
        require_once 'Zend/Http/Client/Adapter/Exception.php';
                throw new Zend_Http_Client_Adapter_Exception('Cannot handle "' .
                    $headers['transfer-encoding'] . '" transfer encoding');
            }

            // We automatically decode chunked-messages when writing to a stream
            // this means we have to disallow the Zend_Http_Response to do it again
            if ($this->out_stream) {
                $response = str_ireplace("Transfer-Encoding: chunked\r\n", '', $response);
            }
        // Else, if we got the content-length header, read this number of bytes
        } elseif (isset($headers['content-length'])) {

            // If we got more than one Content-Length header (see ZF-9404) use
            // the last value sent
            if (is_array($headers['content-length'])) {
                $contentLength = $headers['content-length'][count($headers['content-length']) - 1];
            } else {
                $contentLength = $headers['content-length'];
            }

            $current_pos = ftell($this->socket);
            $chunk = '';

            for ($read_to = $current_pos + $contentLength;
                 $read_to > $current_pos;
                 $current_pos = ftell($this->socket)) {

                 if($this->out_stream) {
                     if(@stream_copy_to_stream($this->socket, $this->out_stream, $read_to - $current_pos) == 0) {
                          $this->_checkSocketReadTimeout();
                          break;
                     }
                 } else {
                    $chunk = @fread($this->socket, $read_to - $current_pos);
                    if ($chunk === false || strlen($chunk) === 0) {
                        $this->_checkSocketReadTimeout();
                        break;
                    }

                    $response .= $chunk;
                }

                // Break if the connection ended prematurely
                if (feof($this->socket)) break;
            }

        // Fallback: just read the response until EOF
        } else {

            do {
                if($this->out_stream) {
                    if(@stream_copy_to_stream($this->socket, $this->out_stream) == 0) {
                          $this->_checkSocketReadTimeout();
                          break;
                     }
                }  else {
                    $buff = @fread($this->socket, 8192);
                    if ($buff === false || strlen($buff) === 0) {
                        $this->_checkSocketReadTimeout();
                        break;
                    } else {
                        $response .= $buff;
                    }
                }

            } while (feof($this->socket) === false);

            $this->close();
        }

        // Close the connection if requested to do so by the server
        if (isset($headers['connection']) && $headers['connection'] == 'close') {
            $this->close();
        }

        return $response;
    }
}


