var page = require('webpage').create(),
    system = require('system'),
    address, output, size;

// Set page user agent so hits are not included in Webtrends Analysis
page.settings.userAgent = 'WebTrends';

page.onResourceRequested = function(request) {
  if (/dcs\.gif/.test(request.url)) {
    console.log(request.url);
  }
};

if (system.args.length == 2) {

  page.open(system.args[1], function (status) {
        if (status !== 'success') {
            console.log('Unable to load the address!');
            phantom.exit(1);
        } else {
            window.setTimeout(function () {
                phantom.exit();
            }, 10000);
        }
    }
  );
} else {
  console.log("Failed to open " + system.args[1]);
  phantom.exit(1);
}
