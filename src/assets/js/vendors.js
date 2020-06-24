/**
 * noty.js
 * @see https://github.com/needim/noty
 */
!function(t,e){"object"==typeof exports&&"object"==typeof module?module.exports=e():"function"==typeof define&&define.amd?define("Noty",[],e):"object"==typeof exports?exports.Noty=e():t.Noty=e()}(this,function(){return i={},n.m=o=[function(t,e,o){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.css=e.deepExtend=e.animationEndEvents=void 0;var r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t};e.inArray=function(t,e,o){var n=void 0;if(o){for(n in e)if(e.hasOwnProperty(n)&&e[n]===t)return!0}else for(n in e)if(e.hasOwnProperty(n)&&e[n]===t)return!0;return!1},e.stopPropagation=function(t){void 0!==(t=t||window.event).stopPropagation?t.stopPropagation():t.cancelBubble=!0},e.generateID=function(){var t="noty_"+(0<arguments.length&&void 0!==arguments[0]?arguments[0]:"")+"_";return t+="xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g,function(t){var e=16*Math.random()|0;return("x"===t?e:3&e|8).toString(16)})},e.outerHeight=function(t){var e=t.offsetHeight,o=window.getComputedStyle(t);return e+=parseInt(o.marginTop)+parseInt(o.marginBottom)},e.addListener=c,e.hasClass=l,e.addClass=function(t,e){var o=h(t),n=o+e;if(l(o,e))return;t.className=n.substring(1)},e.removeClass=function(t,e){var o=h(t),n=void 0;if(!l(t,e))return;n=o.replace(" "+e+" "," "),t.className=n.substring(1,n.length-1)},e.remove=d,e.classList=h,e.visibilityChangeFlow=function(){var t=void 0,e=void 0;void 0!==document.hidden?(t="hidden",e="visibilitychange"):void 0!==document.msHidden?(t="msHidden",e="msvisibilitychange"):void 0!==document.webkitHidden&&(t="webkitHidden",e="webkitvisibilitychange");function o(){n.PageHidden?setTimeout(function(){Object.keys(n.Store).forEach(function(t){n.Store.hasOwnProperty(t)&&n.Store[t].options.visibilityControl&&n.Store[t].stop()})},100):setTimeout(function(){Object.keys(n.Store).forEach(function(t){n.Store.hasOwnProperty(t)&&n.Store[t].options.visibilityControl&&n.Store[t].resume()}),n.queueRenderAll()},100)}e&&c(document,e,function(){n.PageHidden=document[t],o()});c(window,"blur",function(){n.PageHidden=!0,o()}),c(window,"focus",function(){n.PageHidden=!1,o()})},e.createAudioElements=function(t){{var o;t.hasSound&&(o=document.createElement("audio"),t.options.sounds.sources.forEach(function(t){var e=document.createElement("source");e.src=t,e.type="audio/"+t.match(/\.([^.]+)$/)[1],o.appendChild(e)}),t.barDom?t.barDom.appendChild(o):document.querySelector("body").appendChild(o),o.volume=t.options.sounds.volume,t.soundPlayed||(o.play(),t.soundPlayed=!0),o.onended=function(){d(o)})}};var n=function(t){{if(t&&t.__esModule)return t;var e={};if(null!=t)for(var o in t)Object.prototype.hasOwnProperty.call(t,o)&&(e[o]=t[o]);return e.default=t,e}}(o(1));e.animationEndEvents="webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend";e.deepExtend=function t(e){e=e||{};for(var o=1;o<arguments.length;o++){var n=arguments[o];if(n)for(var i in n)n.hasOwnProperty(i)&&(!Array.isArray(n[i])&&"object"===r(n[i])&&null!==n[i]?e[i]=t(e[i],n[i]):e[i]=n[i])}return e};var s,i;e.css=(s=["Webkit","O","Moz","ms"],i={},function(t,e){var o=arguments,n=void 0,i=void 0;if(2===o.length)for(n in e)e.hasOwnProperty(n)&&void 0!==(i=e[n])&&e.hasOwnProperty(n)&&u(t,n,i);else u(t,o[1],o[2])});function a(t){return t=t.replace(/^-ms-/,"ms-").replace(/-([\da-z])/gi,function(t,e){return e.toUpperCase()}),i[t]||(i[t]=function(t){var e=document.body.style;if(t in e)return t;for(var o=s.length,n=t.charAt(0).toUpperCase()+t.slice(1),i=void 0;o--;)if((i=s[o]+n)in e)return i;return t}(t))}function u(t,e,o){e=a(e),t.style[e]=o}function c(t,e,o){var n=3<arguments.length&&void 0!==arguments[3]&&arguments[3];e=e.split(" ");for(var i=0;i<e.length;i++)document.addEventListener?t.addEventListener(e[i],o,n):document.attachEvent&&t.attachEvent("on"+e[i],o)}function l(t,e){return 0<=("string"==typeof t?t:h(t)).indexOf(" "+e+" ")}function d(t){t.parentNode&&t.parentNode.removeChild(t)}function h(t){return(" "+(t&&t.className||"")+" ").replace(/\s+/gi," ")}},function(t,o,e){"use strict";Object.defineProperty(o,"__esModule",{value:!0}),o.Defaults=o.Store=o.Queues=o.DefaultMaxVisible=o.docTitle=o.DocModalCount=o.PageHidden=void 0,o.getQueueCounts=function(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"global",o=0,t=a;u.hasOwnProperty(e)&&(t=u[e].maxVisible,Object.keys(c).forEach(function(t){c[t].options.queue!==e||c[t].closed||o++}));return{current:o,maxVisible:t}},o.addToQueue=function(t){u.hasOwnProperty(t.options.queue)||(u[t.options.queue]={maxVisible:a,queue:[]});u[t.options.queue].queue.push(t)},o.removeFromQueue=function(e){{var o;u.hasOwnProperty(e.options.queue)&&(o=[],Object.keys(u[e.options.queue].queue).forEach(function(t){u[e.options.queue].queue[t].id!==e.id&&o.push(u[e.options.queue].queue[t])}),u[e.options.queue].queue=o)}},o.queueRender=l,o.queueRenderAll=function(){Object.keys(u).forEach(function(t){l(t)})},o.ghostFix=function(t){var e=n.generateID("ghost"),o=document.createElement("div");o.setAttribute("id",e),n.css(o,{height:n.outerHeight(t.barDom)+"px"}),t.barDom.insertAdjacentHTML("afterend",o.outerHTML),n.remove(t.barDom),o=document.getElementById(e),n.addClass(o,"noty_fix_effects_height"),n.addListener(o,n.animationEndEvents,function(){n.remove(o)})},o.build=function(t){!function(t){if(t.options.container)return t.layoutDom=document.querySelector(t.options.container);var e="noty_layout__"+t.options.layout;t.layoutDom=document.querySelector("div#"+e),t.layoutDom||(t.layoutDom=document.createElement("div"),t.layoutDom.setAttribute("id",e),t.layoutDom.setAttribute("role","alert"),t.layoutDom.setAttribute("aria-live","polite"),n.addClass(t.layoutDom,"noty_layout"),document.querySelector("body").appendChild(t.layoutDom))}(t);var e='<div class="noty_body">'+t.options.text+"</div>"+function(e){if(d(e)){var o=document.createElement("div");return n.addClass(o,"noty_buttons"),Object.keys(e.options.buttons).forEach(function(t){o.appendChild(e.options.buttons[t].dom)}),e.options.buttons.forEach(function(t){o.appendChild(t.dom)}),o.outerHTML}return""}(t)+'<div class="noty_progressbar"></div>';t.barDom=document.createElement("div"),t.barDom.setAttribute("id",t.id),n.addClass(t.barDom,"noty_bar noty_type__"+t.options.type+" noty_theme__"+t.options.theme),t.barDom.innerHTML=e,p(t,"onTemplate")},o.hasButtons=d,o.handleModal=function(t){t.options.modal&&(0===i&&function(){var t=document.querySelector("body"),e=document.createElement("div");n.addClass(e,"noty_modal"),t.insertBefore(e,t.firstChild),n.addClass(e,"noty_modal_open"),n.addListener(e,n.animationEndEvents,function(){n.removeClass(e,"noty_modal_open")})}(),o.DocModalCount=i+=1)},o.handleModalClose=function(t){{var e;t.options.modal&&0<i&&(o.DocModalCount=--i,i<=0&&((e=document.querySelector(".noty_modal"))&&(n.removeClass(e,"noty_modal_open"),n.addClass(e,"noty_modal_close"),n.addListener(e,n.animationEndEvents,function(){n.remove(e)}))))}},o.queueClose=h,o.dequeueClose=f,o.fire=p,o.openFlow=function(t){p(t,"afterShow"),h(t),n.addListener(t.barDom,"mouseenter",function(){f(t)}),n.addListener(t.barDom,"mouseleave",function(){h(t)})},o.closeFlow=function(t){delete c[t.id],t.closing=!1,p(t,"afterClose"),n.remove(t.barDom),0!==t.layoutDom.querySelectorAll(".noty_bar").length||t.options.container||n.remove(t.layoutDom);(n.inArray("docVisible",t.options.titleCount.conditions)||n.inArray("docHidden",t.options.titleCount.conditions))&&s.decrement();l(t.options.queue)};var n=function(t){{if(t&&t.__esModule)return t;var e={};if(null!=t)for(var o in t)Object.prototype.hasOwnProperty.call(t,o)&&(e[o]=t[o]);return e.default=t,e}}(e(0));o.PageHidden=!1;var i=o.DocModalCount=0,r={originalTitle:null,count:0,changed:!1,timer:-1},s=o.docTitle={increment:function(){r.count++,s._update()},decrement:function(){r.count--,r.count<=0?s._clear():s._update()},_update:function(){var t=document.title;r.changed?document.title="("+r.count+") "+r.originalTitle:(r.originalTitle=t,document.title="("+r.count+") "+t,r.changed=!0)},_clear:function(){r.changed&&(r.count=0,document.title=r.originalTitle,r.changed=!1)}},a=o.DefaultMaxVisible=5,u=o.Queues={global:{maxVisible:a,queue:[]}},c=o.Store={};o.Defaults={type:"alert",layout:"topRight",theme:"mint",text:"",timeout:!1,progressBar:!0,closeWith:["click"],animation:{open:"noty_effects_open",close:"noty_effects_close"},id:!1,force:!1,killer:!1,queue:"global",container:!1,buttons:[],callbacks:{beforeShow:null,onShow:null,afterShow:null,onClose:null,afterClose:null,onClick:null,onHover:null,onTemplate:null},sounds:{sources:[],volume:1,conditions:[]},titleCount:{conditions:[]},modal:!1,visibilityControl:!1};function l(){var t,e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"global";!u.hasOwnProperty(e)||(t=u[e].queue.shift())&&t.show()}function d(t){return!(!t.options.buttons||!Object.keys(t.options.buttons).length)}function h(t){t.options.timeout&&(t.options.progressBar&&t.progressDom&&n.css(t.progressDom,{transition:"width "+t.options.timeout+"ms linear",width:"0%"}),clearTimeout(t.closeTimer),t.closeTimer=setTimeout(function(){t.close()},t.options.timeout))}function f(t){t.options.timeout&&t.closeTimer&&(clearTimeout(t.closeTimer),t.closeTimer=-1,t.options.progressBar&&t.progressDom&&n.css(t.progressDom,{transition:"width 0ms linear",width:"100%"}))}function p(e,t){e.listeners.hasOwnProperty(t)&&e.listeners[t].forEach(function(t){"function"==typeof t&&t.apply(e)})}},function(t,e,o){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.NotyButton=void 0;var s=function(t){{if(t&&t.__esModule)return t;var e={};if(null!=t)for(var o in t)Object.prototype.hasOwnProperty.call(t,o)&&(e[o]=t[o]);return e.default=t,e}}(o(0));e.NotyButton=function t(e,o,n){var i=this,r=3<arguments.length&&void 0!==arguments[3]?arguments[3]:{};return function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,t),this.dom=document.createElement("button"),this.dom.innerHTML=e,this.id=r.id=r.id||s.generateID("button"),this.cb=n,Object.keys(r).forEach(function(t){i.dom.setAttribute(t,r[t])}),s.addClass(this.dom,o||"noty_btn"),this}},function(t,e,o){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=function(t,e,o){return e&&i(t.prototype,e),o&&i(t,o),t};function i(t,e){for(var o=0;o<e.length;o++){var n=e[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}e.Push=(n(r,[{key:"on",value:function(t,e){var o=1<arguments.length&&void 0!==e?e:function(){};return"function"==typeof o&&this.listeners.hasOwnProperty(t)&&this.listeners[t].push(o),this}},{key:"fire",value:function(t,e){var o=this,n=1<arguments.length&&void 0!==e?e:[];this.listeners.hasOwnProperty(t)&&this.listeners[t].forEach(function(t){"function"==typeof t&&t.apply(o,n)})}},{key:"create",value:function(){console.log("NOT IMPLEMENTED YET")}},{key:"isSupported",value:function(){var t=!1;try{t=window.Notification||window.webkitNotifications||navigator.mozNotification||window.external&&void 0!==window.external.msIsSiteMode()}catch(t){}return t}},{key:"getPermissionStatus",value:function(){var t="default";if(window.Notification&&window.Notification.permissionLevel)t=window.Notification.permissionLevel;else if(window.webkitNotifications&&window.webkitNotifications.checkPermission)switch(window.webkitNotifications.checkPermission()){case 1:t="default";break;case 0:t="granted";break;default:t="denied"}else window.Notification&&window.Notification.permission?t=window.Notification.permission:navigator.mozNotification?t="granted":window.external&&void 0!==window.external.msIsSiteMode()&&(t=window.external.msIsSiteMode()?"granted":"default");return t.toString().toLowerCase()}},{key:"getEndpoint",value:function(t){var e=t.endpoint,o=t.subscriptionId;return o&&-1===e.indexOf(o)&&(e+="/"+o),e}},{key:"isSWRegistered",value:function(){try{return"activated"===navigator.serviceWorker.controller.state}catch(t){return!1}}},{key:"unregisterWorker",value:function(){var s=this;"serviceWorker"in navigator&&navigator.serviceWorker.getRegistrations().then(function(t){var e=!0,o=!1,n=void 0;try{for(var i,r=t[Symbol.iterator]();!(e=(i=r.next()).done);e=!0)i.value.unregister(),s.fire("onSubscriptionCancel")}catch(t){o=!0,n=t}finally{try{!e&&r.return&&r.return()}finally{if(o)throw n}}})}},{key:"requestSubscription",value:function(t){function e(t){"granted"===t?(o.fire("onPermissionGranted"),"serviceWorker"in navigator?navigator.serviceWorker.register(o.workerPath).then(function(){navigator.serviceWorker.ready.then(function(t){i.fire("onWorkerSuccess"),t.pushManager.subscribe({userVisibleOnly:n}).then(function(t){var e=t.getKey("p256dh"),o=t.getKey("auth");i.subData={endpoint:i.getEndpoint(t),p256dh:e?window.btoa(String.fromCharCode.apply(null,new Uint8Array(e))):null,auth:o?window.btoa(String.fromCharCode.apply(null,new Uint8Array(o))):null},i.fire("onSubscriptionSuccess",[i.subData])}).catch(function(t){i.fire("onWorkerError",[t])})})}):i.fire("onWorkerNotSupported")):"denied"===t&&(o.fire("onPermissionDenied"),o.unregisterWorker())}var o=this,n=!(0<arguments.length&&void 0!==t)||t,i=this,r=this.getPermissionStatus();"default"===r?window.Notification&&window.Notification.requestPermission?window.Notification.requestPermission(e):window.webkitNotifications&&window.webkitNotifications.checkPermission&&window.webkitNotifications.requestPermission(e):e(r)}}]),r);function r(){var t=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"/service-worker.js";return function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,r),this.subData={},this.workerPath=t,this.listeners={onPermissionGranted:[],onPermissionDenied:[],onSubscriptionSuccess:[],onSubscriptionCancel:[],onWorkerError:[],onWorkerSuccess:[],onWorkerNotSupported:[]},this}},function(e,t,z){(function(I,R){var t;t=function(){"use strict";function c(t){return"function"==typeof t}var o=Array.isArray?Array.isArray:function(t){return"[object Array]"===Object.prototype.toString.call(t)},n=0,e=void 0,i=void 0,a=function(t,e){h[n]=t,h[n+1]=e,2===(n+=2)&&(i?i(f):b())};var t="undefined"!=typeof window?window:void 0,r=t||{},s=r.MutationObserver||r.WebKitMutationObserver,u="undefined"==typeof self&&void 0!==I&&"[object process]"==={}.toString.call(I),l="undefined"!=typeof Uint8ClampedArray&&"undefined"!=typeof importScripts&&"undefined"!=typeof MessageChannel;function d(){var t=setTimeout;return function(){return t(f,1)}}var h=new Array(1e3);function f(){for(var t=0;t<n;t+=2){(0,h[t])(h[t+1]),h[t]=void 0,h[t+1]=void 0}n=0}var p,m,v,y,b=void 0;function w(t,e){var o=arguments,n=this,i=new this.constructor(k);void 0===i[_]&&V(i);var r,s=n._state;return s?(r=o[s-1],a(function(){return W(s,i,r,n._result)})):j(n,i,t,e),i}function g(t){if(t&&"object"==typeof t&&t.constructor===this)return t;var e=new this(k);return O(e,t),e}b=u?function(){return I.nextTick(f)}:s?(m=0,v=new s(f),y=document.createTextNode(""),v.observe(y,{characterData:!0}),function(){y.data=m=++m%2}):l?((p=new MessageChannel).port1.onmessage=f,function(){return p.port2.postMessage(0)}):(void 0===t?function(){try{var t=z(9);return void 0!==(e=t.runOnLoop||t.runOnContext)?function(){e(f)}:d()}catch(t){return d()}}:d)();var _=Math.random().toString(36).substring(16);function k(){}var C=void 0,S=1,D=2,x=new L;function E(t){try{return t.then}catch(t){return x.error=t,x}}function P(t,n,i){a(function(e){var o=!1,t=function(t,e,o,n){try{t.call(e,o,n)}catch(t){return t}}(i,n,function(t){o||(o=!0,(n!==t?O:M)(e,t))},function(t){o||(o=!0,q(e,t))},e._label);!o&&t&&(o=!0,q(e,t))},t)}function T(t,e,o){var n,i;e.constructor===t.constructor&&o===w&&e.constructor.resolve===g?(n=t,(i=e)._state===S?M(n,i._result):i._state===D?q(n,i._result):j(i,void 0,function(t){return O(n,t)},function(t){return q(n,t)})):o===x?(q(t,x.error),x.error=null):void 0!==o&&c(o)?P(t,e,o):M(t,e)}function O(t,e){var o,n;t===e?q(t,new TypeError("You cannot resolve a promise with itself")):(n=typeof(o=e),null===o||"object"!=n&&"function"!=n?M(t,e):T(t,e,E(e)))}function A(t){t._onerror&&t._onerror(t._result),N(t)}function M(t,e){t._state===C&&(t._result=e,t._state=S,0!==t._subscribers.length&&a(N,t))}function q(t,e){t._state===C&&(t._state=D,t._result=e,a(A,t))}function j(t,e,o,n){var i=t._subscribers,r=i.length;t._onerror=null,i[r]=e,i[r+S]=o,i[r+D]=n,0===r&&t._state&&a(N,t)}function N(t){var e=t._subscribers,o=t._state;if(0!==e.length){for(var n=void 0,i=void 0,r=t._result,s=0;s<e.length;s+=3)n=e[s],i=e[s+o],n?W(o,n,i,r):i(r);t._subscribers.length=0}}function L(){this.error=null}var H=new L;function W(t,e,o,n){var i=c(o),r=void 0,s=void 0,a=void 0,u=void 0;if(i){if((r=function(t,e){try{return t(e)}catch(t){return H.error=t,H}}(o,n))===H?(u=!0,s=r.error,r.error=null):a=!0,e===r)return void q(e,new TypeError("A promises callback cannot return that same promise."))}else r=n,a=!0;e._state!==C||(i&&a?O(e,r):u?q(e,s):t===S?M(e,r):t===D&&q(e,r))}var Q=0;function V(t){t[_]=Q++,t._state=void 0,t._result=void 0,t._subscribers=[]}function B(t,e){this._instanceConstructor=t,this.promise=new t(k),this.promise[_]||V(this.promise),o(e)?(this.length=e.length,this._remaining=e.length,this._result=new Array(this.length),0===this.length?M(this.promise,this._result):(this.length=this.length||0,this._enumerate(e),0===this._remaining&&M(this.promise,this._result))):q(this.promise,new Error("Array Methods must be provided an Array"))}function F(t){this[_]=Q++,this._result=this._state=void 0,this._subscribers=[],k!==t&&("function"!=typeof t&&function(){throw new TypeError("You must pass a resolver function as the first argument to the promise constructor")}(),this instanceof F?function(e,t){try{t(function(t){O(e,t)},function(t){q(e,t)})}catch(t){q(e,t)}}(this,t):function(){throw new TypeError("Failed to construct 'Promise': Please use the 'new' operator, this object constructor cannot be called as a function.")}())}return B.prototype._enumerate=function(t){for(var e=0;this._state===C&&e<t.length;e++)this._eachEntry(t[e],e)},B.prototype._eachEntry=function(e,t){var o,n,i=this._instanceConstructor,r=i.resolve;r===g?(o=E(e))===w&&e._state!==C?this._settledAt(e._state,t,e._result):"function"!=typeof o?(this._remaining--,this._result[t]=e):i===F?(T(n=new i(k),e,o),this._willSettleAt(n,t)):this._willSettleAt(new i(function(t){return t(e)}),t):this._willSettleAt(r(e),t)},B.prototype._settledAt=function(t,e,o){var n=this.promise;n._state===C&&(this._remaining--,t===D?q(n,o):this._result[e]=o),0===this._remaining&&M(n,this._result)},B.prototype._willSettleAt=function(t,e){var o=this;j(t,void 0,function(t){return o._settledAt(S,e,t)},function(t){return o._settledAt(D,e,t)})},F.all=function(t){return new B(this,t).promise},F.race=function(i){var r=this;return o(i)?new r(function(t,e){for(var o=i.length,n=0;n<o;n++)r.resolve(i[n]).then(t,e)}):new r(function(t,e){return e(new TypeError("You must pass an array to race."))})},F.resolve=g,F.reject=function(t){var e=new this(k);return q(e,t),e},F._setScheduler=function(t){i=t},F._setAsap=function(t){a=t},F._asap=a,F.prototype={constructor:F,then:w,catch:function(t){return this.then(null,t)}},F.polyfill=function(){var t=void 0;if(void 0!==R)t=R;else if("undefined"!=typeof self)t=self;else try{t=Function("return this")()}catch(t){throw new Error("polyfill failed because global object is unavailable in this environment")}var e=t.Promise;if(e){var o=null;try{o=Object.prototype.toString.call(e.resolve())}catch(t){}if("[object Promise]"===o&&!e.cast)return}t.Promise=F},F.Promise=F},e.exports=t()}).call(t,z(7),z(8))},function(t,e){},function(t,e,o){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=function(t,e,o){return e&&i(t.prototype,e),o&&i(t,o),t};function i(t,e){for(var o=0;o<e.length;o++){var n=e[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}o(5);var r,s=o(4),a=(r=s)&&r.__esModule?r:{default:r},u=h(o(0)),c=h(o(1)),l=o(2),d=o(3);function h(t){if(t&&t.__esModule)return t;var e={};if(null!=t)for(var o in t)Object.prototype.hasOwnProperty.call(t,o)&&(e[o]=t[o]);return e.default=t,e}var f,p=(n(m,[{key:"on",value:function(t,e){var o=1<arguments.length&&void 0!==e?e:function(){};return"function"==typeof o&&this.listeners.hasOwnProperty(t)&&this.listeners[t].push(o),this}},{key:"show",value:function(){var o=this;if(this.showing||this.shown)return this;!0===this.options.killer?m.closeAll():"string"==typeof this.options.killer&&m.closeAll(this.options.killer);var t,e=c.getQueueCounts(this.options.queue);return e.current>=e.maxVisible||c.PageHidden&&this.options.visibilityControl?(c.addToQueue(this),c.PageHidden&&this.hasSound&&u.inArray("docHidden",this.options.sounds.conditions)&&u.createAudioElements(this),c.PageHidden&&u.inArray("docHidden",this.options.titleCount.conditions)&&c.docTitle.increment()):(c.Store[this.id]=this,c.fire(this,"beforeShow"),this.showing=!0,this.closing?this.showing=!1:(c.build(this),c.handleModal(this),this.options.force?this.layoutDom.insertBefore(this.barDom,this.layoutDom.firstChild):this.layoutDom.appendChild(this.barDom),this.hasSound&&!this.soundPlayed&&u.inArray("docVisible",this.options.sounds.conditions)&&u.createAudioElements(this),u.inArray("docVisible",this.options.titleCount.conditions)&&c.docTitle.increment(),this.shown=!0,this.closed=!1,c.hasButtons(this)&&Object.keys(this.options.buttons).forEach(function(e){var t=o.barDom.querySelector("#"+o.options.buttons[e].id);u.addListener(t,"click",function(t){u.stopPropagation(t),o.options.buttons[e].cb(o)})}),this.progressDom=this.barDom.querySelector(".noty_progressbar"),u.inArray("click",this.options.closeWith)&&(u.addClass(this.barDom,"noty_close_with_click"),u.addListener(this.barDom,"click",function(t){u.stopPropagation(t),c.fire(o,"onClick"),o.close()},!1)),u.addListener(this.barDom,"mouseenter",function(){c.fire(o,"onHover")},!1),this.options.timeout&&u.addClass(this.barDom,"noty_has_timeout"),this.options.progressBar&&u.addClass(this.barDom,"noty_has_progressbar"),u.inArray("button",this.options.closeWith)&&(u.addClass(this.barDom,"noty_close_with_button"),t=document.createElement("div"),u.addClass(t,"noty_close_button"),t.innerHTML="×",this.barDom.appendChild(t),u.addListener(t,"click",function(t){u.stopPropagation(t),o.close()},!1)),c.fire(this,"onShow"),null===this.options.animation.open?this.promises.show=new a.default(function(t){t()}):"function"==typeof this.options.animation.open?this.promises.show=new a.default(this.options.animation.open.bind(this)):(u.addClass(this.barDom,this.options.animation.open),this.promises.show=new a.default(function(t){u.addListener(o.barDom,u.animationEndEvents,function(){u.removeClass(o.barDom,o.options.animation.open),t()})})),this.promises.show.then(function(){var t=o;setTimeout(function(){c.openFlow(t)},100)}))),this}},{key:"stop",value:function(){return c.dequeueClose(this),this}},{key:"resume",value:function(){return c.queueClose(this),this}},{key:"setTimeout",value:(f=function(t){var e;return this.stop(),this.options.timeout=t,this.barDom&&(this.options.timeout?u.addClass(this.barDom,"noty_has_timeout"):u.removeClass(this.barDom,"noty_has_timeout"),e=this,setTimeout(function(){e.resume()},100)),this},v.toString=function(){return f.toString()},v)},{key:"setText",value:function(t,e){var o=1<arguments.length&&void 0!==e&&e;return this.barDom&&(this.barDom.querySelector(".noty_body").innerHTML=t),o&&(this.options.text=t),this}},{key:"setType",value:function(t,e){var o=this,n=1<arguments.length&&void 0!==e&&e;return this.barDom&&(u.classList(this.barDom).split(" ").forEach(function(t){"noty_type__"===t.substring(0,11)&&u.removeClass(o.barDom,t)}),u.addClass(this.barDom,"noty_type__"+t)),n&&(this.options.type=t),this}},{key:"setTheme",value:function(t,e){var o=this,n=1<arguments.length&&void 0!==e&&e;return this.barDom&&(u.classList(this.barDom).split(" ").forEach(function(t){"noty_theme__"===t.substring(0,12)&&u.removeClass(o.barDom,t)}),u.addClass(this.barDom,"noty_theme__"+t)),n&&(this.options.theme=t),this}},{key:"close",value:function(){var e=this;return this.closed||(this.shown?(c.fire(this,"onClose"),this.closing=!0,null===this.options.animation.close||!1===this.options.animation.close?this.promises.close=new a.default(function(t){t()}):"function"==typeof this.options.animation.close?this.promises.close=new a.default(this.options.animation.close.bind(this)):(u.addClass(this.barDom,this.options.animation.close),this.promises.close=new a.default(function(t){u.addListener(e.barDom,u.animationEndEvents,function(){e.options.force?u.remove(e.barDom):c.ghostFix(e),t()})})),this.promises.close.then(function(){c.closeFlow(e),c.handleModalClose(e)}),this.closed=!0):c.removeFromQueue(this)),this}}],[{key:"closeAll",value:function(t){var e=0<arguments.length&&void 0!==t&&t;return Object.keys(c.Store).forEach(function(t){e?c.Store[t].options.queue===e&&c.Store[t].killable&&c.Store[t].close():c.Store[t].killable&&c.Store[t].close()}),this}},{key:"clearQueue",value:function(t){var e=0<arguments.length&&void 0!==t?t:"global";return c.Queues.hasOwnProperty(e)&&(c.Queues[e].queue=[]),this}},{key:"overrideDefaults",value:function(t){return c.Defaults=u.deepExtend({},c.Defaults,t),this}},{key:"setMaxVisible",value:function(t,e){var o=0<arguments.length&&void 0!==t?t:c.DefaultMaxVisible,n=1<arguments.length&&void 0!==e?e:"global";return c.Queues.hasOwnProperty(n)||(c.Queues[n]={maxVisible:o,queue:[]}),c.Queues[n].maxVisible=o,this}},{key:"button",value:function(t,e,o,n){var i=1<arguments.length&&void 0!==e?e:null,r=3<arguments.length&&void 0!==n?n:{};return new l.NotyButton(t,i,o,r)}},{key:"version",value:function(){return"3.2.0-beta"}},{key:"Push",value:function(t){return new d.Push(t)}},{key:"Queues",get:function(){return c.Queues}},{key:"PageHidden",get:function(){return c.PageHidden}}]),m);function m(){var t=0<arguments.length&&void 0!==arguments[0]?arguments[0]:{};return function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,m),this.options=u.deepExtend({},c.Defaults,t),c.Store[this.options.id]?c.Store[this.options.id]:(this.id=this.options.id||u.generateID("bar"),this.closeTimer=-1,this.barDom=null,this.layoutDom=null,this.progressDom=null,this.showing=!1,this.shown=!1,this.closed=!1,this.closing=!1,this.killable=this.options.timeout||0<this.options.closeWith.length,this.hasSound=0<this.options.sounds.sources.length,this.soundPlayed=!1,this.listeners={beforeShow:[],onShow:[],afterShow:[],onClose:[],afterClose:[],onClick:[],onHover:[],onTemplate:[]},this.promises={show:null,close:null},this.on("beforeShow",this.options.callbacks.beforeShow),this.on("onShow",this.options.callbacks.onShow),this.on("afterShow",this.options.callbacks.afterShow),this.on("onClose",this.options.callbacks.onClose),this.on("afterClose",this.options.callbacks.afterClose),this.on("onClick",this.options.callbacks.onClick),this.on("onHover",this.options.callbacks.onHover),this.on("onTemplate",this.options.callbacks.onTemplate),this)}function v(t){return f.apply(this,arguments)}e.default=p,"undefined"!=typeof window&&u.visibilityChangeFlow(),t.exports=e.default},function(t,e){var o,n,i=t.exports={};function r(){throw new Error("setTimeout has not been defined")}function s(){throw new Error("clearTimeout has not been defined")}function a(e){if(o===setTimeout)return setTimeout(e,0);if((o===r||!o)&&setTimeout)return o=setTimeout,setTimeout(e,0);try{return o(e,0)}catch(t){try{return o.call(null,e,0)}catch(t){return o.call(this,e,0)}}}!function(){try{o="function"==typeof setTimeout?setTimeout:r}catch(t){o=r}try{n="function"==typeof clearTimeout?clearTimeout:s}catch(t){n=s}}();var u,c=[],l=!1,d=-1;function h(){l&&u&&(l=!1,u.length?c=u.concat(c):d=-1,c.length&&f())}function f(){if(!l){var t=a(h);l=!0;for(var e=c.length;e;){for(u=c,c=[];++d<e;)u&&u[d].run();d=-1,e=c.length}u=null,l=!1,function(e){if(n===clearTimeout)return clearTimeout(e);if((n===s||!n)&&clearTimeout)return n=clearTimeout,clearTimeout(e);try{n(e)}catch(t){try{return n.call(null,e)}catch(t){return n.call(this,e)}}}(t)}}function p(t,e){this.fun=t,this.array=e}function m(){}i.nextTick=function(t){var e=new Array(arguments.length-1);if(1<arguments.length)for(var o=1;o<arguments.length;o++)e[o-1]=arguments[o];c.push(new p(t,e)),1!==c.length||l||a(f)},p.prototype.run=function(){this.fun.apply(null,this.array)},i.title="browser",i.browser=!0,i.env={},i.argv=[],i.version="",i.versions={},i.on=m,i.addListener=m,i.once=m,i.off=m,i.removeListener=m,i.removeAllListeners=m,i.emit=m,i.prependListener=m,i.prependOnceListener=m,i.listeners=function(t){return[]},i.binding=function(t){throw new Error("process.binding is not supported")},i.cwd=function(){return"/"},i.chdir=function(t){throw new Error("process.chdir is not supported")},i.umask=function(){return 0}},function(t,e){var o=function(){return this}();try{o=o||Function("return this")()||(0,eval)("this")}catch(t){"object"==typeof window&&(o=window)}t.exports=o},function(t,e){}],n.c=i,n.i=function(t){return t},n.d=function(t,e,o){n.o(t,e)||Object.defineProperty(t,e,{configurable:!1,enumerable:!0,get:o})},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="",n(n.s=6);function n(t){if(i[t])return i[t].exports;var e=i[t]={i:t,l:!1,exports:{}};return o[t].call(e.exports,e,e.exports,n),e.l=!0,e.exports}var o,i});
