const path = require( 'path' );
const { CleanWebpackPlugin } = require( 'clean-webpack-plugin' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );

module.exports = ( env, args ) => {
    const mode = args.mode ?? 'production';

    const config = {
        entry: {
			main: './src/main.js',
			temp: './src/temp.js',
		},
        output: {
            filename: '[name].js',
            path: path.resolve( __dirname, './public' ),
            publicPath: '/',
        },
        mode,
        module: {
            rules: [
                {
                    test: /\.css$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        {
                            loader: 'css-loader',
                            options: {
                                sourceMap: true,
                                url: false,
                            },
                        },
                    ],
                },
                {
                    test: /\.scss$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        {
                            loader: 'css-loader',
                            options: {
                                sourceMap: true,
                                url: false,
                            },
                        },
                        {
                            loader: 'postcss-loader',
                            options: {
                                postcssOptions: {
                                    plugins: [
                                        [ 'autoprefixer' ],
										'postcss-merge-queries',
                                    ],
                                },
                                sourceMap: true,
                            },
                        },
                        {
                            loader: 'sass-loader',
                            options: {
                                sourceMap: true,
                            },
                        },
                    ],
                },
            ],
        },
        plugins: [
            new CleanWebpackPlugin( {
                cleanStaleWebpackAssets: false,
            } ),
            new MiniCssExtractPlugin(),
        ],
    };

    if ( 'development' === mode ) {
        config.devtool = 'source-map';
    }

    return config;
};
