<?php
/**
 * ErrorController
 * Kļūdu apstrāde
 */

class ErrorController {
    public function notFound() {
        http_response_code(404);
        view('errors/404');
    }

    public function forbidden() {
        http_response_code(403);
        view('errors/403');
    }

    public function serverError() {
        http_response_code(500);
        view('errors/500');
    }
}
