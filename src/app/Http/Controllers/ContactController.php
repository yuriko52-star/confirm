<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Contact;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
// 現在日時の取得、フォーマットの変更、タイムゾーン設定、日時計算が可能
use Symfony\Component\HttpFoundation\StreamedResponse;
// Symfonyのコンポーネントで、データをストリーミング形式で送信するためのレスポンスクラス。主にCSVや大規模データの出力で使用される

class ContactController extends Controller
{
    public function index()
    {
         $categories = Category::all();
        // 先にカテゴリのテーブル、モデル、シーダーファイルを作成してから。viewにセレクトボックスの中身も表示させる。

         return view ('contact',compact('categories'));
        
        
    }
    public function confirm (ContactRequest $request)
    {
        $contacts = $request->all();
        // カラムがたくさんあるので、この表示か？
        $category = Category::find($request->category_id);
        // セレクトボックスから一つを選択するので、findを使う
        return view('confirm',compact('contacts','category'));
         
    }
    
    public function store (Contactrequest $request)
    {   
        // テーブルに保存
       if($request->has('back')) {
        return redirect('/')->withInput();
       }
        //backは、修正リンクのname値のことか？
        // LaravelのwithInput()メソッド、リダイレクト時に直前のリクエストで送信されたフォームデータをセッションに保存し、次のリクエストで利用できるようにするためのもの。このメソッドを使うと、例えばフォームに入力したデータがバリデーションエラーなどでリダイレクトされても、入力内容が保持されるので、ユーザーが再入力する手間を省くことができる。
       $request['tell'] = $request->tel_1 . $request->tel_2 . $request->tel_3;
    
       Contact::create(
        $request->only([
            'category_id',
            'first_name',
            'last_name',
            'gender',
            'email',
            'tell',
            'address',
            'building',
            'detail',
        ])
        );
        return view('thanks');
    }
    public function admin () {
        $contacts = Contact::with('category')->paginate(7);
        // ページネーション
        $categories = Category::all();
        $csvData = Contact::all();
        
        return view('admin',compact('contacts','categories','csvData'));
    }
    public function search(Request $request)
    {
        if($request->has('reset')) {
            return redirect('/admin')->withInput();
        }
        // リセットボタンを押したときにresetパラメータが送信される
        $query = Contact::query();
        // 検索条件を追加
        $query = $this->getSearchQuery($request ,$query);
        // 検索条件を組み立てる
        $contacts = $query->paginate(7);
        $csvData = $query->get();
        // すべての検索結果を取得、データはcsvDataに保存される
        $categories = Category::all();
        return view ('admin',compact('contacts','csvData','categories'));
       
    }
    public function destroy(Request $request)
    {
        Contact::find($request->id)->delete();
        return redirect('/admin');
    }
     public function export(Request $request)
    //  CSVエクスポートを実行するためのメソッド
    {
        $query = Contact::query();
        // Contactモデルに基づいてクエリビルダーを作成
        $query = $this->getSearchQuery($request, $query);
        // getSearchQueryメソッドを使い、ユーザーが指定した条件をクエリに追加
        $csvData = $query->get()->toArray();
        // 条件に一致するデータをデータベースから取得、 結果を配列形式に変換
        $csvHeader = [
            'id', 'category_id', 'first_name', 'last_name', 'gender', 'email', 'tell', 'address', 'building', 'detail', 'created_at', 'updated_at'
        ];

        $response = new StreamedResponse(function () use ($csvHeader, $csvData) {
            $createCsvFile = fopen('php://output', 'w');
                // PHPの特殊な出力ストリームphp://outputを使用して、直接レスポンスとしてCSVを作成。
            mb_convert_variables('SJIS-win', 'UTF-8', $csvHeader);
                // CSVファイルをShift_JISで出力するため、ヘッダー情報をUTF-8からShift_JISに変換。
                // 日本語文字列を含む場合、文字化け防止のために必要
            fputcsv($createCsvFile, $csvHeader);
                // ヘッダー行をCSVファイルに書き込む
                // 下はデータ行の書き込み　日付フォーマットの変換
            foreach ($csvData as $csv) {
                $csv['created_at'] = Date::make($csv['created_at'])->setTimezone('Asia/Tokyo')->format('Y/m/d H:i:s');
                $csv['updated_at'] = Date::make($csv['updated_at'])->setTimezone('Asia/Tokyo')->format('Y/m/d H:i:s');
                fputcsv($createCsvFile, $csv);
                
            }

            fclose($createCsvFile);
            // ファイルの終了処理
        }, 200,
        // HTTPステータスコード（成功を意味する200）
         [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="contacts.csv"',
        ]);
        // ヘッダー情報　Content-Type: ファイルがCSV形式であることを指定。Content-Disposition: ファイル名をcontacts.csvに設定し、ブラウザがダウンロードするように指示。

        return $response;
        // ストリームレスポンスをブラウザに返し、ユーザーがCSVファイルをダウンロードできるようにする
        // 動作のまとめ　検索条件に基づいてデータベースからデータを取得。ヘッダーとデータをCSV形式に変換。日本語の文字化け防止のためにShift_JISで出力。ストリームレスポンスとしてデータを逐次ブラウザに送信。ブラウザはcontacts.csvとしてダウンロード。
    }

    private function getSearchQuery($request, $query)
    {
        if(!empty($request->keyword)) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->keyword . '%')
                    ->orWhere('last_name', 'like', '%' . $request->keyword . '%')
                    ->orWhere('email', 'like', '%' . $request->keyword . '%');
            });
            // 無名関数を使い、複数の条件をまとめて追加orWhere: 他のカラム（last_nameやemail）も同じキーワードで部分一致をチェック。
        }

        if (!empty($request->gender)) {
            $query->where('gender', '=', $request->gender);
            // 性別がリクエストに含まれる場合、genderカラムがその値と一致するレコードを検索
        }

        if (!empty($request->category_id)) {
            $query->where('category_id', '=', $request->category_id);
            // 指定されたカテゴリIDと一致するレコードを検索
        }

        if (!empty($request->date)) {
            $query->whereDate('created_at', '=', $request->date);
            // whereDate: データベースのcreated_atカラムの日付部分が指定された値と一致するレコードを検索
        }

        return $query;
    }
    // 全体の流れ　ユーザーが入力した検索条件をチェック。条件に基づいてクエリを構築。最終的に構築されたクエリを返す。
    


}