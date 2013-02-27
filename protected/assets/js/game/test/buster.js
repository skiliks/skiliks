var config = module.exports;
var path = require('path');
//noinspection JSCheckFunctionSignatures
config["My Tests"] = {
    autoRun: false,
    env: "browser",
    rootPath: path.join("..", ".."),
    libs: [
        path.join("jquery", "*.js"),
        "jquery.ddslick.min.js",
        path.join("tag-handler", "jquery.taghandler.min.js"),
        "underscore.js",
        "backbone.js",
        "require.js",
    ],
    sources: [
        "text.js",
        path.join("game", "jst", "**", "*.jst"),    // Paths are relative to config file
        path.join("game", "models", "**", "*.js"),    // Paths are relative to config file
        path.join("game", "models", "*.js"),    // Paths are relative to config file
        path.join("game", "collections", "**", "*.js"),    // Paths are relative to config file
        path.join("game", "collections", "*.js"),
        path.join("game", "views", "mail", "*.js"),    // Paths are relative to config file
        path.join("game", "views", "*.js"),
        path.join("game", "views", "world", "*.js")

    ],
    tests: [
        path.join("game", "test", "*-test.js")
    ],
    extensions: [require('buster-amd')]
};