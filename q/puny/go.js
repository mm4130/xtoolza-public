
var go=(function(global){"use strict";var go={'VERSION':"2.0-beta"},ROOT_DIR,doc=global.document,loader,anticache;go.include=function include(names,listener){loader.include(names,listener);};go.module=function module(name,deps,CModule){if(!CModule){CModule=deps;deps=[];}
loader.loaded(name,deps,CModule);};go.getRootDir=function getRootDir(){return ROOT_DIR;};go.__Loader=(function(){function Loader(includer,creator){this.__construct(includer,creator);}
Loader.prototype={'__construct':function(includer,creator){this.includer=includer;this.creator=creator;this.modules={};this.preloaded={};},'include':function(names,listener){var len,i,name,module,includer=this.includer,counter,Listeners=go.__Loader.Listeners;if(typeof names==="string"){names=[names];}
if(listener){counter=Listeners.createCounter(null,listener);}
for(i=0,len=names.length;i<len;i+=1){name=names[i];module=this.modules[name];if(!module){module={};this.modules[name]=module;if(this.preloaded[name]){this.loaded.apply(this,this.preloaded[name]);module=this.modules[name];this.preloaded[name]=null;}else{includer(name);}}
if((!module.created)&&counter){counter.inc();if(module.listener){module.listener.append(counter);}else{module.listener=Listeners.create(counter);}}}
if(counter){counter.filled();}},'loaded':function(name,deps,data){var module=this.modules[name],listener,loader=this;if(!module){module={};this.modules[name]=module;}else if(module.created||module.data){return;}
listener=function(){loader.creator.call(this,name,data);module.created=true;if(module.listener){module.listener.call(null);}};deps=deps||[];this.include(deps,listener);},'preload':function(name,deps,data){if(!this.preloaded.hasOwnProperty(name)){this.preloaded[name]=[name,deps,data];}},'createPreloaded':function(){var preloaded=this.preloaded,name;for(name in preloaded){if(preloaded.hasOwnProperty(name)){if(preloaded[name]){this.loaded.apply(this,preloaded[name]);}}}},'includer':null,'creator':null,'modules':null,'preloaded':null};return Loader;}());go.__Loader.Listeners={'create':(function(){var ping,append,remove;ping=function ping(){this.apply(null,arguments);};append=function append(handler,check){var handlers=this._handlers,len,i;if(check){for(i=0,len=handlers.length;i<len;i+=1){if(handlers[i]===handler){return i;}}}
handlers.push(handler);return handlers.length-1;};remove=function remove(handler,all){var handlers=this._handlers,len,i,removed=false;if(typeof handler==="function"){for(i=0,len=handlers.length;i<len;i+=1){if(handlers[i]===handler){handlers[i]=null;removed=true;if(!all){break;}}}}else{if(handlers[handler]){handlers[handler]=null;removed=true;}}
return removed;};function create(handlers){var listener;if(typeof handlers==="function"){handlers=[handlers];}else if(!handlers){handlers=[];}
listener=function(){var handler,len=handlers.length,i;for(i=0;i<len;i+=1){handler=handlers[i];if(handler){handler.apply(null,arguments);}}};listener._handlers=handlers;listener.ping=ping;listener.append=append;listener.remove=remove;return listener;}
return create;}()),'createCounter':(function(){var inc,filled;inc=function inc(i){if(this._count!==0){this._count+=(i||1);}
return this.count;};filled=function filled(){if(typeof this._count!=="number"){this._count=0;this._handler.apply(null);return true;}
return false;};function createCounter(count,handler){if(typeof count==="string"){count=parseInt(count,10)||0;}
if(count===0){handler();}
function Counter(){if(Counter._count>0){Counter._count-=1;if(Counter._count===0){Counter._handler.apply(null);}}}
Counter._count=count;Counter._handler=handler;Counter.inc=inc;Counter.filled=filled;return Counter;}
return createCounter;}())};go.__Loader.includeJSFile=function(src){doc.write('<script type="text/javascript" src="'+src+'"></script>');};loader=(function(){function includer(name){go.__Loader.includeJSFile(ROOT_DIR+name+".js"+anticache);}
function creator(name,data){go[name]=data(go,global);}
return new go.__Loader(includer,creator);}());go.log=function(){var console=global.console;if(console&&console.log){console.log.apply(console,arguments);}};(function(){var SRC_PATTERN=new RegExp("^(.*\\/)?go\\.js(\\?[^#]*)?(#(.*?))?$"),matches;if(doc.currentScript){matches=SRC_PATTERN.exec(doc.currentScript.getAttribute("src"));}
if(!matches){matches=(function(){var scripts=doc.getElementsByTagName("script"),i,src,matches;for(i=scripts.length;i>0;i-=1){src=scripts[i-1].getAttribute("src");matches=SRC_PATTERN.exec(src);if(matches){return matches;}}
return null;}());}
if(!matches){throw new Error("go.js is not found in DOM");}
ROOT_DIR=matches[1];anticache=matches[2]||"";if(matches[4]){go.include(matches[4].split(","));}}());return go;}(this));go.module("Lang",function(go,global,undefined){"use strict";var Lang,nativeObject=global.Object,nativeToString=nativeObject.prototype.toString,nativeSlice=Array.prototype.slice,nativeIsArray=Array.isArray,nativeGetPrototypeOf=nativeObject.getPrototypeOf,nativeKeys=Object.keys,nativeMap=Array.prototype.map;Lang={'bind':function bind(func,thisArg,args){var result;thisArg=thisArg||global;if(typeof func.bind==="function"){if(args){args=[thisArg].concat(args);}else{args=[thisArg];}
result=func.bind.apply(func,args);}else if(args){result=function binded(){return func.apply(thisArg,args.concat(nativeSlice.call(arguments,0)));};}else{result=function binded(){return func.apply(thisArg,arguments);};}
return result;},'bindMethod':function bindMethod(context,methodName,args){var f;if(args&&args.length){f=function bindedMethod(){return context[methodName].apply(context,args.concat(nativeSlice.call(arguments)));};}else{f=function bindedMethod(){return context[methodName].apply(context,arguments);};}
return f;},'getType':function getType(value){var type,name;if(value&&(typeof value.go$type==="string")){return value.go$type;}
type=typeof value;if((type!=="object")&&(type!=="function")){return type;}
if(value===null){return"null";}
if(!getType._str){getType._str={'[object Function]':"function",'[object Array]':"array",'[object RegExp]':"regexp",'[object Error]':"error",'[object Date]':"date",'[object Text]':"textnode",'[object Arguments]':"arguments",'[object Number]':"number",'[object String]':"string",'[object Boolean]':"boolean",'[object NodeList]':"collection",'[object HTMLCollection]':"collection"};}
name=nativeToString.call(value);type=getType._str[name];if(type){return type;}
if(name.indexOf("[object HTML")===0){return"element";}
if(!(value instanceof Object)){if(value.nodeType===1){return"element";}
if(value.nodeType===3){return"textnode";}
if(value.item){for(name in value){if(name==="item"){break;}}
if(name!=="item"){return"collection";}}
if((value+":").indexOf("function")!==-1){return"function";}}
if(typeof value.length==="number"){for(name in value){if(name==="length"){return"object";}}
return"arguments";}
return"object";},'isDict':function isDict(value){if((!value)||(typeof value!=="object")){return false;}
if(value.constructor===Object){if(nativeGetPrototypeOf&&(nativeGetPrototypeOf(value)!==Object.prototype)){return false;}
return true;}
if(value instanceof Object){return false;}
if(nativeGetPrototypeOf){value=Object.getPrototypeOf(value);if(!value){return false;}
return(Object.getPrototypeOf(value)===null);}
try{return((value.constructor+":").indexOf("function Object()")!==-1);}catch(e){return false;}
return false;},'isArray':function isArray(value,strict){if(strict){return Lang.isStrictArray(value);}
switch(Lang.getType(value)){case"array":return true;case"collection":case"arguments":return(!strict);default:return false;}},'isStrictArray':(function(value){if(nativeIsArray){return nativeIsArray;}
return function isStrictArray(value){return(nativeToString.call(value)==="[object Array]");};}()),'toArray':function toArray(value){var len,i,result;switch(Lang.getType(value)){case"array":return value;case"arguments":return nativeSlice.call(value,0);case"collection":result=[];for(i=0,len=value.length;i<len;i+=1){result.push(value[i]);}
return result;case"undefined":case"null":return[];case"object":if(!Lang.isDict(value)){return[value];}
result=[];for(i in value){if(value.hasOwnProperty(i)){result.push(value[i]);}}
return result;default:return[value];}},'inArray':function inArray(needle,haystack){var i,len;if(Array.prototype.indexOf){return(Array.prototype.indexOf.call(haystack,needle)!==-1);}
for(i=0,len=haystack.length;i<len;i+=1){if(haystack[i]===needle){return true;}}
return false;},'getObjectKeys':function(object){var k,keys;if(nativeKeys){return nativeKeys(object);}
keys=[];for(k in object){if(object.hasOwnProperty(k)){keys.push(k);}}
return keys;},'each':function each(items,callback,thisArg,deep){var result,i,len;thisArg=thisArg||global;if(Lang.isArray(items)){if(nativeMap){return nativeMap.call(items,callback,thisArg);}
result=[];for(i=0,len=items.length;i<len;i+=1){result.push(callback.call(thisArg,items[i],i,items));}}else{result={};for(i in items){if(items.hasOwnProperty(i)||deep){result[i]=callback.call(thisArg,items[i],i,items);}}}
return result;},'copy':function copy(source){var result,i,len;if(Lang.isArray(source)){result=[];for(i=0,len=source.length;i<len;i+=1){result.push(source[i]);}}else{result=Lang.extend({},source,false);}
return result;},'extend':function extend(destination,source,deep){var k;for(k in source){if(deep||source.hasOwnProperty(k)){destination[k]=source[k];}}
return destination;},'merge':function merge(destination,source){var k,value;for(k in source){if(source.hasOwnProperty(k)){value=source[k];if(Lang.isDict(value)&&Lang.isDict(destination[k])){destination[k]=Lang.merge(destination[k],value);}else{destination[k]=value;}}}
return destination;},'inherit':(function(){var inherit,nativeCreate,Fake;nativeCreate=nativeObject.create;if(!nativeCreate){Fake=function(){};}
return function inherit(Constr,Parent,extend){var proto;Constr=Constr||function EmptyConstructor(){};Parent=Parent||nativeObject;if(nativeCreate){proto=nativeCreate(Parent.prototype);}else{Fake.prototype=Parent.prototype;proto=new Fake();}
if(extend){proto=Lang.extend(proto,extend);}
proto.constructor=Constr;Constr.prototype=proto;return Constr;};}()),'f':{'empty':function(){},'ffalse':function(){return false;},'ftrue':function(){return true;},'identity':function(value){return value;},'once':function(f){var called=false,result;return function(){if(called){return result;}
result=f.apply((this||global),arguments);called=true;return result;};},'compose':function(funcs,context){var len=funcs.length;if(len===0){return Lang.f.identity;}
return function(){var i,value;value=funcs[0].apply(context,arguments);for(i=1;i<len;i+=1){value=funcs[i].call(context,value);}
return value;};}},'Listeners':go.__Loader.Listeners,'eoc':null};Lang.Exception=(function(){var Base,create,Block,isFileName=(Error.prototype.fileName!==undefined),inherit=Lang.inherit;create=function create(name,parent,defmessage){var Exception,regexp;parent=parent||Base;defmessage=defmessage||"";Exception=function Exception(message){var e=new Error(),matches;this.stack=e.stack;this.name=name;this.message=(message!==undefined)?message:defmessage;if(isFileName){if(!regexp){regexp=new RegExp("^.*\n.*@(.*):(.*)\n");}
matches=regexp.exec(e.stack+"\n");if(matches){this.fileName=matches[1];this.lineNumber=parseInt(matches[2],10);}}};return inherit(Exception,parent);};Block=function Block(exceptions,ns,base,lazy){this._exceptions=exceptions;this._ns=ns?ns+".":"";if(base===false){base=Base;}else if(typeof base!=="function"){if(typeof base!=="string"){base="Base";}
this[base]=create(this.ns+base,Base,ns+" base exception");base=this[base];}
this._base=base;if(!lazy){this.createAll();}};Block.prototype.get=function get(name){var parent,message,exception;if(this.hasOwnProperty(name)){return this[name];}
parent=this._exceptions[name];if((typeof parent==="object")&&parent){message=parent[1];parent=parent[0];}
if(parent===undefined){return null;}
switch(typeof parent){case"function":break;case"string":parent=this.get(parent);break;default:parent=this._base;}
exception=create(this._ns+name,parent,message);this[name]=exception;return exception;};Block.prototype.raise=function raise(name,message){var E=this.get(name);throw new E(message);};Block.prototype.createAll=function createAll(){var exceptions=this._exceptions,name;for(name in exceptions){if(exceptions.hasOwnProperty(name)){this.get(name);}}};Base=create("go.Exception",Error);Base.Base=Base;Base.create=create;Base.Block=Block;return Base;}());return Lang;});