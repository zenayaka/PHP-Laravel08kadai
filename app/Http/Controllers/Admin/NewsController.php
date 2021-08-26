<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
//Controller.phpを呼び出している
use App\News;

use App\History;

use Carbon\Carbon;

class NewsController extends Controller
{
  public function add()
  {
      return view('admin.news.create');
  }
  public function create(Request $request)
  {
      $this->validate($request, News::$rules);
      //thisは疑似変数と呼ばれる　オブジェクトへの参考を意味する
      //Varidateメソッドとは、エラーメッセージと入力値と共に直前のページに戻る機能を持っている
      //News::$rulesはNews.phpファイルの$rules変数を呼び出すための書き方になる
      $news = new News;
      //newはModelからインスタンスを生成するメソッド
      //Model…Laravelにおいてデータベースとやり取りをする機能のこと
      $form = $request->all();
      //formで入力された値を取得することができる
      if (isset($form['image'])) {
            //issetメソッドは引数のなかにデータがあるかないかを判断するメソッド
            //投稿画面で画像を選択していれば$form['image']にデータが入り、選択していなければnullになっている
        $path = $request->file('image')->store('public/image');
        
        $news->image_path = basename($path);
        //news->image_path=null;はNewsテーブルのimage_pathカラムにnullを代入する
      } else {
            
          $news->image_path = null;
          //Newsテーブルのimage_pathカラムにnullを代入する
      }
      unset($form['_token']);
      //フォームから送信されてきた_tokenを削除する
      unset($form['image']);
      //フォームから送信されてきた_imageを削除する
      $news->fill($form);
      //fillメソッドを使うことでtitle,body,image_pathの値にデータを入れることができる
      $news->save();
      //保存を意味する
      return redirect('admin/news/create');
      //「admin/news/create.blade.php」に移動
  }
  public function index(Request $request)
  {
      $cond_title = $request->cond_title;
      //$requestの中のcond_titleの値を$cond_titleに代入している
      //もしrequestにcond_titleがなければnullが代入される
      if ($cond_title != '') {
          $posts = News::where('title', $cond_title)->get();
          //newsテーブルの中のtitleカラムで$cond_title(ユーザーが入力した文字)に一致するレコードをすべて取得することができる
      } else {
          // それ以外はすべてのニュースを取得する
          $posts = News::all();
          //News Modelを使ってデータベースに保存されているnewsテーブルのレコードをすべて取得し、変数postに代入している
      }
      return view('admin.news.index', ['posts' => $posts, 'cond_title' => $cond_title]);
      //Requestにcond_titleをおくっている
  }
  
  public function edit(Request $request)
  {
      // News Modelからデータを取得する
      $news = News::find($request->id);
      if (empty($news)) {
        abort(404);    
      }
      return view('admin.news.edit', ['news_form' => $news]);
  }
    public function update(Request $request)
    {
        $this->validate($request, News::$rules);
        $news = News::find($request->id);
        $news_form = $request->all();
        if ($request->remove == 'true') {
            $news_form['image_path'] = null;
        } elseif ($request->file('image')) {
            $path = $request->file('image')->store('public/image');
            $news_form['image_path'] = basename($path);
        } else {
            $news_form['image_path'] = $news->image_path;
        }
        
        unset($news_form['_token']);
        unset($news_form['image']);
        unset($news_form['remove']);
        $news->fill($news_form)->save();
        
        $history = new History;
        $history->news_id = $news->id;
        $history->edited_at = Carbon::now();
        $history->save();
        
        return redirect('admin/news/');
    }
    
}