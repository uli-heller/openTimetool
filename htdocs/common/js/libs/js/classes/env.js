/////////////////////////////////////////////////
//
//		file: 		env.js
//
//		enviroment detection, etc.
//
//		get the browser type, get interesting enviroment
//		variables
//
//      $Log: env.js,v $
//      Revision 1.1.2.1  2003/11/09 14:40:54  wk
//      *** empty log message ***
//
//      Revision 1.10  2003/03/15 22:50:07  cain
//      - added mozilla detection
//
//      Revision 1.9  2003/02/09 03:18:55  cain
//      - improve IE detection
//
//      Revision 1.8  2003/02/03 05:34:21  cain
//      - detect opera 7
//
//      Revision 1.7  2003/01/31 06:13:21  cain
//      - opera 7 does dom :-)
//
//      Revision 1.6  2002/09/21 09:34:34  cain
//      - bugfixed the opera check, since it if the opera identified as MSIE also left it set
//
//      Revision 1.5  2002/01/18 05:53:04  cain
//      - now we detect Konqueror too
//
//      Revision 1.4  2001/06/18 22:08:31  cain
//      - added opera 5 recognition
//
//      Revision 1.3  2001/06/12 22:36:13  cain
//      - added the konqueror to accept it as a domable browser, which is kinda generous
//
//      Revision 1.2  2001/03/28 18:59:26  cain
//      no message
//
//
/////////////////////////////////////////////////////////////


/////////////////////////////////////////////////
//
//		determine the browser used
//
function userAgent()
{
    var agent   = navigator.userAgent.toLowerCase();

    this.major  = parseInt(navigator.appVersion);
    this.minor  = parseFloat(navigator.appVersion);

    //  alert("agent: "+agent+"\r\nnavVersion"+navigator.appVersion+"\r\nmajor: "+this.major+"\r\nminor: "+this.minor);

    this.ns     = (
                    ( agent.indexOf('mozilla')!=-1) &&
                    ( ( agent.indexOf('spoofer')==-1) && (agent.indexOf('compatible') == -1) ) &&
                    ( agent.indexOf('netscape6')==-1) // otherwise is.ns will be true and is.dom too
                  );
    this.ns2    = (this.ns && (this.major == 3));
    this.ns3    = (this.ns && (this.major == 3));
    this.ns4    = (this.ns && (this.major == 4));

    this.ie     = (agent.indexOf("msie") != -1);
    if (this.ie) {
        var regExpr = /msie.?([^;]+)/i;
        regExpr.exec(agent);
        this.version = RegExp.$1;
        this.ie3    = parseInt(this.version) == 2;
        this.ie4    = parseInt(this.version) == 4;
        this.ie5    = parseInt(this.version) == 5;
        this.ie6    = parseInt(this.version) == 6;
    }

    this.kon    = (agent.indexOf("konqueror") != -1);

    // returns something like that for agent if it simulates a NS: mozilla/4.73 (windows 98; u) opera 4.02 [en]
    // if it should be simply itself: opera/4.02 (windows 98; u) [en]
    // or Opera/5.0 (Linux ...) [en]
    // and the navigator version is the one which is set in the opera setting, since the opera
    // can do kinda camouflage to say "i am a NS", so the navVersion cant be used
    this.op     = (agent.indexOf("opera") != -1);
    if( this.op )
    {
        operaVersion = agent.split("opera");
        operaVersion = operaVersion[1];
        operaVersion = operaVersion.replace(/\//,'');
        
        this.version = operaVersion;
        this.op3    = (parseInt(operaVersion) == 3);
        this.op4    = (parseInt(operaVersion) == 4);
        this.op5    = (parseInt(operaVersion) == 5);
        this.op7    = (parseInt(operaVersion) == 7);

        // set all others to false, since it is no other browser, but an opera :-)
        this.ie = this.ie4 = this.ie3 = false;
        this.ns = this.ns4 = this.ns3 = this.ns2 = false;
        this.kon = false;
        //this.ie4 = true;

        //alert(operaVersion+" parseint "+parseInt(operaVersion)+"\nop4="+this.op4+"    op3="+this.op3+"    op5="+this.op5);
    }
    if (this.op7) {
        this.dom = true;
    }

    if (agent.indexOf("gecko")!=-1) {
        this.moz = true;
        var regExpr = /.*(\d.\d)\) gecko.*/i;
        regExpr.exec(agent);
        this.version = RegExp.$1;
    }
    
    // netscape 6 PR3 returns something like this for agent: mozilla/5.0 (windows; u; win98; en-us; m18) gecko/20000929 netscape6/6.03b
    // the version: 5.0 (Windows; en-us)
    this.dom =  (agent.indexOf("netscape6") != -1) ||
                            (agent.indexOf("gecko") != -1) ||
                            (agent.indexOf("konqueror/2.") != -1) ||
                            this.op || 
                            (this.ie && this.version>4);	//opera doesnt really get it
//if(this.dom)    alert("cool a domable browser"); else    alert("not domable :-(");
}

/////////////////////////////////////////////////
//
//      determine the available height/width
//
function Env()
{
	this.screenWidth=screen.width;
	this.screenHeight=screen.height;

//  try     this doesnt work with NS
  if(document)  // BUG - this doesnt work, if u include the js-files before the body taag it doesnt bring the alert-warning
  {
  	if(is.ns4)
	  {
   		this.availableWidth=innerWidth;
	    this.availableHeight=innerHeight;
  	}
	  if(is.ie4)
  	{
 	  	this.availableWidth=document.body.clientWidth;
	    this.availableHeight=document.body.clientHeight;
  	}
	  if(is.dom)
  	{
 	  	this.availableWidth = window.innerWidth;
	    this.availableHeight = window.innerHeight;
  	}
  }
//  catch(error)
  else
  {
    alert('please include the env.js after the "body" tag so the document`s properties can be read');
  }

  this.getEnv = env_get;

  // even better if we have some event "onChangeSize" or so which does this automatically !!!
}

function env_get()
{
    Env(); // re-get the environment
}

var userAgent = new userAgent();    // instanciate an object of class userAgent
var is = userAgent;         // this is deprecated, but still very much in use

var env = new Env;		// get enviroment
