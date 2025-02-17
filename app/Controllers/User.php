<?php

namespace App\Controllers;

use App\Models\User_Model;
use App\Models\Kategori_Model;

class User extends BaseController
{
    public function home()
    {
        $artikelModel = new User_Model();
        $article = $artikelModel->findAll();

        $data = [
            'current_page' => 'home', // Definisi $current_page
            'page' => 'user/home',
            'artikel' => $article
        ];

        // dd($article[0]['judul']);

        return view('template', $data);
    }

    public function artikel($id)
    {
        $artikelModel = new User_Model();
        $article = $artikelModel->find($id);

        if ($article) {
            if ($article['status'] === 'aktif') {
                $data = [
                    'current_page' => 'home',
                    'page' => 'user/artikel',
                    'artikel' => $article
                ];
                return view('template', $data);
            } else {
                return view('errors/html/error');
            }
        } else {
            return view('errors/html/error');
        }
    }

    public function artkat($id_kategori)
    {
        $kategoriModel = new Kategori_Model();
        $categories = $kategoriModel->getCategories();
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : '';

        $articles = $kategoriModel->getArticlesByKategori($id_kategori, $search, $sort);

        // Tambahkan logika pengurutan
        $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
        if ($sort === 'asc') {
            usort($articles, function ($a, $b) {
                return strtotime($a['timestamp']) - strtotime($b['timestamp']);
            });
        } elseif ($sort === 'desc') {
            usort($articles, function ($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });
        }

        // cari nama kategori berdasarkan id_kategori yang dipilih menggunakan array_filter
        $kategori = array_filter($categories, function ($kategori) use ($id_kategori) {
            return $kategori['id_kategori'] == $id_kategori;
        });

        $data = [
            'current_page' => 'kategori',
            'page' => 'user/artkat',
            'articles' => $articles,
            'categories' => $categories,
            'kategori_nama' => !empty($kategori) ? reset($kategori)['nama_kategori'] : ''
        ];

        return view('template', $data);
    }
}