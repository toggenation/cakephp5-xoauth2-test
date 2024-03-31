<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Code Controller
 */
class CodeController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {

        // http://127.0.0.1:8080/getToken.php?code=4/0AeaYSHDjt5X-9rj0E_3N59gPfXHha16tcOwtk7WLdKWtj8IESOViYikwqvdtQ2LpS3jG-Q&scope=https://www.googleapis.com/auth/gmail.compose%20https://www.googleapis.com/auth/gmail.addons.current.action.compose%20https://www.googleapis.com/auth/gmail.send
        $params = $this->request->getQueryParams();

        $this->set(compact('params'));
    }
}
