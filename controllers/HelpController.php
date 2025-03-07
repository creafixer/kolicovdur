<?php
/**
 * Yardım Controller
 */
class HelpController extends Controller {
    /**
     * Yardım sayfası
     */
    public function index() {
        $this->render('help/index', [
            'pageTitle' => 'Yardım',
            'activePage' => 'help'
        ]);
    }
}