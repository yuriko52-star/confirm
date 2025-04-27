@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/contact.css') }}">
@endsection

@section('content')
<div class="contact-form">
  <h2 class="contact-form__heading content__heading" >Contact</h2>
  <!-- タイトルだけなら囲まない -->
  <div class="contact-form__inner">
    <form action="/confirm" method="post">
      @csrf
      <!-- 項目と中身を書いていく -->
       <!-- お名前 -->
        <div class="contact-form__group contact-form__name-group">
          <!-- 項目 -->
          <label class="contact-form__label">
            お名前<span class="contact-form__required">※</span>
          </label>
          <!-- 中身 -->
          <div class="contact-form__name-inputs">
            <input type="text" class="contact-form__input contact-form__name-input" name="first_name" value="{{old('first_name')}}">
            <input type="text" class="contact-form__input contact-form__name-input" name="last_name" value="{{old('last_name')}}">
          </div>
        
        <!-- エラー -->
         <div class="contact-form__error-message">
          @if($errors->has('first_name'))
          <p class="contact-form__error-message-first-name">{{$errors->first('first_name')}}</p>
          @endif
          @if($errors->has('last_name'))
          <p class="contact-form__error-message-last-name">{{$errors->first('last_name')}}</p>
          @endif
         </div>
        </div>

        <!-- 性別 -->
         <div class="contact-form__group">
          <label class="contact-form__label">
            性別<span class="contact-form__required">※</span>
          </label>
          <!-- 中身 -->
          <div class="contact-form__gender-inputs">
            <!-- 男性 -->
            <div class="contact-form__gender-option">
              <label class="contact-form__gender-label">
                <input type="radio" class="contact-form__gender-input" name="gender" value="1"{{old('gender')==1 || old('gender')==null ? 'checked' : '' }}>
                <!-- 理解がムズイところ -->
            <span class="contact-form__gender-text">男性</span>
              </label>
            </div>
            <!-- 女性 -->
            <div class="contact-form__gender-option">
              <label class="contact-form__gender-label">
                <input type="radio" class="contact-form__gender-input" name="gender" value="2"{{old('gender')==2 ? 'checked' : '' }}>
                 <!-- 理解がムズイところ -->
            <span class="contact-form__gender-text">女性</span>
              </label>
            </div>
            <!-- その他 -->
            <div class="contact-form__gender-option">
              <label class="contact-form__gender-label">
                <input type="radio" class="contact-form__gender-input" name="gender" value="3"{{old('gender')==3 ? 'checked' : '' }}>
                 <!-- 理解がムズイところ -->
            <span class="contact-form__gender-text">その他</span>
              </label>
            </div>
          </div>
          <!-- エラー -->
           <p class="contact-form__error-message">
             @error('gender')
             {{$message}}
             @enderror
           </p>
          </div>
          
          <!--メールアドレス  -->
        <div class="contact-form__group">
          <!-- 項目 -->
          <label class="contact-form__label">
            メールアドレス<span class="contact-form__required">※</span>
          </label>
          <!-- 1つだけなので囲まない -->
          <input type="email" class="contact-form__input" name="email" value="{{old('email')}}">
          <!-- エラー -->
          <p class="contact-form__error-message">
             @error('email')
             {{$message}}
             @enderror
          </p>
        </div> 

        <!-- 電話番号 -->
         <div class="contact-form__group">
          <!-- 項目 -->
          <label class="contact-form__label">
            電話番号<span class="contact-form__required">※</span>
          </label>
          <!-- 項目　複数あるので囲む -->
           <div class="contact-form__tel-inputs">
            <input type="tel" class="contact-form__input contact-form__tel-input" name="tel_1" value="{{old('tel_1')}}">
            <span>-</span>
            <input type="tel" class="contact-form__input contact-form__tel-input"name="tel_2" value="{{old('tel_2')}}">
            <span>-</span>
            <input type="tel" class="contact-form__input contact-form__tel-input"name="tel_3" value="{{old('tel_3')}}">
           </div>
           <!-- エラー -->
           <p class="contact-form__error-message">
            @if($errors->has('tel_1')) 
            {{$errors->first('tel_1')}}
            @elseif($errors->has('tel_2'))
            {{$errors->first('tel_2')}}
            @else
            {{$errors->first('tel_3')}}
            @endif
          </p>
        </div>
        <!-- 住所 -->
          <div class="contact-form__group">
          <!-- 項目 -->
          <label class="contact-form__label">
            住所<span class="contact-form__required">※</span>
          </label>
          <!-- 1つだけなので囲まない -->
          <input type="text" class="contact-form__input" name="address" value="{{old('address')}}">
          <!-- エラー -->
          <p class="contact-form__error-message">
             @error('address')
             {{$message}}
             @enderror
          </p>
        </div> 
        <!-- 建物名 -->
        <div class="contact-form__group">
          <!-- 項目 -->
          <label class="contact-form__label">
            建物名
          </label>
          <!-- 1つだけなので囲まない -->
          <input type="text" class="contact-form__input" name="building" value="{{old('building')}}">
        </div> 
        <!-- お問い合わせの種類 -->
         <div class="contact-form__group">
          <!-- 項目 -->
          <label class="contact-form__label">
            お問い合わせの種類<span class="contact-form__required">※</span>
          </label>
         <!-- セレクトボックス -->
          <div class="contact-for__select-inner">
            <select name="category_id" class="contact-form__select">
              <option disabled selected>選択してください</option>
              @foreach($categories as $category)
              <!-- カテゴリをひとつずつ表示 -->
              <option value="{{$category->id}}" {{old('category_id')==$category->id ? 'selected' : ' '}}>{{$category->content}}</option>
              <!--ムズイ。カテゴリからカラムの値を取って入れる -->
              @endforeach
            </select>
            </div>
          <!-- エラー -->
          <p class="contact-form__error-message">
             @error('category_id')
             {{$message}}
             @enderror
          </p>
        </div>
         <!-- お問い合わせ内容 -->
          <div class="contact-form__group">
          <!-- 項目 -->
          <label class="contact-form__label">
            お問い合わせ内容<span class="contact-form__required">※</span>
          </label>
         <!-- テキストエリア　まとめないので囲みなし-->
         <textarea name="detail"  class="contact-form__textarea">
          {{old('detail')}}
         </textarea>
          <!-- エラー -->
          <p class="contact-form__error-message">
             @error('detail')
             {{$message}}
             @enderror
          </p>
        </div>
        <!-- ボタン -->
         <input type="submit" class="contact-form__btn btn" value="確認画面">
    </form>
  </div>
</div>
@endsection
        