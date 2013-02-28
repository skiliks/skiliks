var config = module.exports;
var path = require('path');
var extensions = [require('buster-amd')];
try {
    extensions.push(require("buster-coverage"));
} catch (e) {
    console.warn('No coverage installed');
}
config["SkiliksGame"] = {
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
        "game/views/world/*.js"

    ],
    tests: [
        "game/test/*-test.js"
    ],
    extensions: [require('buster-amd'), require("buster-coverage")],
    "buster-amd": {
        "pathMapper": function (path) {
            return path.replace(/\.js$/, "").replace(/^\//, "");
        }
    },
    "buster-coverage": {
        outputDirectory: "coverage_reports", //Write to this directory instead of coverage
        combinedResultsOnly: true, //Write one combined file instead of one for each browser
        coverageExclusions: ["jst"] //Exclude everything with resources in it's path
    }
};