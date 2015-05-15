# Web-Status

## Documentation
This web interface let's you display your json files in a way that is familiar to the iOS app "Statusboard" by Panic Inc.

It supports digital and analog clocks as reference to the json timestamps and right now three different kinds of charts: line, bar and pie.

All features at a glance:
 - three different 2D charts 
 - optional animation of each chart
 - auto-refresh charts (if specified within the json file)

## Known problems
If the last entry inside a json array ends with a semicolon, Web-Status won't display anything at all.

## License
Web-Status is available under the [MIT license](http://opensource.org/licenses/MIT). Check license file for further information.


## FAQ

#### How does this work?
Web-Status uses php code to parse json files and create javascript/jQuery source code that then generates the charts. jQuery UI is used to enable dragging and resizing.

#### Why does my browser not display Web-Status correctly?
To display the charts html5 is used. Though this is a current standard many (archaic) browsers like IE still do not display everything correctly. Therefore, try using a different browser such as Safari or Chrome.