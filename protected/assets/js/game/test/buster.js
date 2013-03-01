var config = module.exports;
(function () {
    var path = require('path');
    var _extensions = [require('buster-amd')];
    var coverage_installed = false;
    try {
        _extensions.push(require("buster-coverage"));
        coverage_installed = true;
    } catch (e) {
        console.warn('No coverage installed');
    }
    config.SkiliksGame = {
        autoRun: false,
        env: "browser",
        rootPath: "../..",
        libs: [
            "jquery/*.js",
            "jquery.ddslick.min.js",
            "tag-handler/jquery.taghandler.min.js",
            "underscore.js",
            "backbone.js",
            "require.js",
            "game/test/common.js"
        ],
        sources: [
            "text.js",
            "game/jst/**/*.jst",    // Paths are relative to config file
            "game/models/**/*.js",    // Paths are relative to config file
            "game/models/*.js",    // Paths are relative to config file
            "game/collections/**/*.js",    // Paths are relative to config file
            "game/collections/*.js",
            "game/views/mail/*.js",    // Paths are relative to config file
            "game/views/*.js",
            "game/views/dialogs/*.js",
            "game/views/world/*.js"

        ],
        tests: [
            "game/test/*-test.js"
        ],
        extensions: _extensions,
        "buster-amd": {
            "pathMapper": function (path) {
                return path.replace(/\.js$/, "").replace(/^\//, "");
            }
        }
    };
    if (coverage_installed === true) {
    {
        config.SkiliksGame["buster-coverage"] = {
            outputDirectory: "coverage_reports", //Write to this directory instead of coverage
            combinedResultsOnly: true, //Write one combined file instead of one for each browser
            coverageExclusions: ["jst"] //Exclude everything with resources in it's path
        };
    }
    }
})();