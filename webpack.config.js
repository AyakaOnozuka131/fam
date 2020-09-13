// [定数] webpack の出力オプションを指定します
// 'production' か 'development' を指定
const MODE = 'production';

module.exports = {
  // モード値を production に設定すると最適化された状態で、
  // development に設定するとソースマップ有効でJSファイルが出力される
  mode: MODE,
  devtool: "source-map",
  module: {
    rules: [
      // Sassファイルの読み込みとコンパイル
      {
        test: /\.scss/, // 対象となるファイルの拡張子
        use: [
          // linkタグに出力する機能
          'style-loader',
          // CSSをバンドルするための機能
          {
            loader: 'css-loader',
            options: {
              // オプションでCSS内のurl()メソッドの取り込みを禁止する
              url: false,
              // ソースマップの利用有無
              sourceMap: true,
              // 0 => no loaders (default);
              // 1 => postcss-loader;
              // 2 => postcss-loader, sass-loader
              importLoaders: 2
            },
          },
          {
            loader: "postcss-loader",
            options: {
              // PostCSS側でもソースマップを有効にする
              sourceMap: true,
              plugins: [
                // Autoprefixerを有効化
                // ベンダープレフィックスを自動付与する
                require("autoprefixer")({
                  grid: true
                })
              ]
            }
          },  
          {
            loader: 'sass-loader',
            options: {
              // ソースマップの利用有無
              sourceMap: true
            }
          }
        ],
      },
    ],
  },
};