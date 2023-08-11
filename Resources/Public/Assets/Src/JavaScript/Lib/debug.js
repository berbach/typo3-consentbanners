let _debug = {};

_debug.output = true;

_debug.getArguments = function (args) {
    var arr = [];
    for (var i = 0; i < args.length; i++) {
        arr[i] = args[i];
    }
    return arr;
};

_debug.info = function () {
    this.write("info", this.getArguments(arguments));
};

_debug.log = function () {
    this.write("log", this.getArguments(arguments));
};

_debug.warn = function () {
    this.write("warn", this.getArguments(arguments));
};

_debug.error = function () {
    this.write("error", this.getArguments(arguments));
};

_debug.debug = function () {
    this.write("debug", this.getArguments(arguments));
};

_debug.write = function (level, args) {
    if (this.output && typeof(console) === "object")
        if (typeof InstallTrigger !== 'undefined')
            console[level].apply(this, args);
        else if ( Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0 )
            window.console.log(args[0]);
        else
            window.console[level](args);
};

module.exports = _debug;
