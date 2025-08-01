const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');

module.exports = {
  entry: {
    main: './src/js/main.js',
    auth: './src/js/auth.js',
    admin: './src/js/admin.js',
    'admin-users': './src/js/admin-users.js',
    products: './src/js/products.js',
    dashboard: './src/js/dashboard.js',
    coex: './src/js/coex.js',
    'profile-flash': './src/js/profile-flash.js',
    standassign: './src/js/standassign.js',
    style: './src/scss/style.scss',
    'admin-style': './src/scss/admin.scss',
  },
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'js/[name].js',
    assetModuleFilename: 'assets/[name][ext]',
    clean: true,
  },
  module: {
    rules: [
      {
        test: /\.(sa|sc|c)ss$/,
        use: [
          MiniCssExtractPlugin.loader,
          { loader: 'css-loader', options: { url: false } },
          'postcss-loader',
          {
            loader: 'sass-loader',
            options: { sassOptions: { quietDeps: true } },
          },
        ],
      },
      {
        test: /\.(png|jpe?g|gif|svg|woff2?|eot|ttf|webp)$/i,
        type: 'asset/resource',
      },
    ],
  },
  externals: {
    jquery: 'jQuery',
  },
  plugins: [
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      'window.jQuery': 'jquery',
    }),
    new MiniCssExtractPlugin({
      filename: 'css/[name].css',
    }),
    new CopyWebpackPlugin({
      patterns: [
        {
          from: 'node_modules/intl-tel-input/build/img',
          to: 'img/',
        },
      ],
    }),
  ],
  resolve: {
    alias: {
      // Alias flag images to your theme’s copied location
      './img/flags.png': path.resolve(__dirname, 'dist/img/flags.png'),
      './img/flags.webp': path.resolve(__dirname, 'dist/img/flags.webp'),
    },
  },
  stats: { children: false },
};
