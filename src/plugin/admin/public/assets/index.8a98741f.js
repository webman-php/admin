import{bp as E,C as j,j as f,aR as R,as as H,a as $,U as N,r as U,m as M,V as z,ac as O,am as W,a_ as q,f0 as J,a7 as X,_ as T,K as k,bc as G,w as Q,v as Y,bd as Z,bS as K,aJ as tt,dm as D,dw as et,f1 as nt,b as rt,f as L,k as _,dE as V,aL as b,o as S,h as ot,aM as P,aN as C,n as at,F as it}from"./index.f8bcf808.js";import{c as I,u as st}from"./index.d3397da6.js";import ct from"./SessionTimeoutLogin.3016095b.js";import{s as lt,g as ut}from"./scrollTo.d51d60f9.js";import"./FullscreenOutlined.f88b6f65.js";import"./index.ffdfd07f.js";import"./useWindowSizeFn.d2a0a89b.js";import"./useContentViewHeight.0d100a60.js";import"./uniqBy.0ae55b98.js";import"./_baseIteratee.f49fbaac.js";import"./index.e344e4ac.js";import"./RedoOutlined.ed3d1686.js";import"./lock.74a6de68.js";import"./Login.efc0eb8b.js";import"./LoginForm.6a9b5b9e.js";import"./index.14ba2351.js";import"./index.0e7d1863.js";import"./LoginFormTitle.4c1672da.js";import"./useLogin.7cc7f83b.js";import"./index.26a767f7.js";import"./index.f2e779e6.js";function pt(e){var t,n=function(s){return function(){t=null,e.apply(void 0,j(s))}},r=function(){if(t==null){for(var s=arguments.length,c=new Array(s),a=0;a<s;a++)c[a]=arguments[a];t=E(n(c))}};return r.cancel=function(){return E.cancel(t)},r}var ft={icon:{tag:"svg",attrs:{viewBox:"64 64 896 896",focusable:"false"},children:[{tag:"path",attrs:{d:"M859.9 168H164.1c-4.5 0-8.1 3.6-8.1 8v60c0 4.4 3.6 8 8.1 8h695.8c4.5 0 8.1-3.6 8.1-8v-60c0-4.4-3.6-8-8.1-8zM518.3 355a8 8 0 00-12.6 0l-112 141.7a7.98 7.98 0 006.3 12.9h73.9V848c0 4.4 3.6 8 8 8h60c4.4 0 8-3.6 8-8V509.7H624c6.7 0 10.4-7.7 6.3-12.9L518.3 355z"}}]},name:"vertical-align-top",theme:"outlined"},vt=ft;function x(e){for(var t=1;t<arguments.length;t++){var n=arguments[t]!=null?Object(arguments[t]):{},r=Object.keys(n);typeof Object.getOwnPropertySymbols=="function"&&(r=r.concat(Object.getOwnPropertySymbols(n).filter(function(o){return Object.getOwnPropertyDescriptor(n,o).enumerable}))),r.forEach(function(o){gt(e,o,n[o])})}return e}function gt(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}var A=function(t,n){var r=x({},t,n.attrs);return f(R,x({},r,{icon:vt}),null)};A.displayName="VerticalAlignTopOutlined";A.inheritAttrs=!1;var mt=A,dt=function(){return{visibilityHeight:{type:Number,default:400},duration:{type:Number,default:450},target:Function,prefixCls:String,onClick:Function}},Tt=$({compatConfig:{MODE:3},name:"ABackTop",inheritAttrs:!1,props:dt(),setup:function(t,n){var r=n.slots,o=n.attrs,s=n.emit,c=N("back-top",t),a=c.prefixCls,g=c.direction,l=U(),m=M({visible:!1,scrollEvent:null}),B=function(){return l.value&&l.value.ownerDocument?l.value.ownerDocument:window},F=function(u){var p=t.target,v=p===void 0?B:p,d=t.duration;lt(0,{getContainer:v,duration:d}),s("click",u)},h=pt(function(i){var u=t.visibilityHeight,p=ut(i.target,!0);m.visible=p>u}),w=function(){var u=t.target,p=u||B,v=p();m.scrollEvent=K(v,"scroll",function(d){h(d)}),h({target:v})},y=function(){m.scrollEvent&&m.scrollEvent.remove(),h.cancel()};return z(function(){return t.target},function(){y(),O(function(){w()})}),W(function(){O(function(){w()})}),q(function(){O(function(){w()})}),J(function(){y()}),X(function(){y()}),function(){var i,u,p=f("div",{class:"".concat(a.value,"-content")},[f("div",{class:"".concat(a.value,"-icon")},[f(mt,null,null)])]),v=T(T({},o),{},{onClick:F,class:(i={},k(i,"".concat(a.value),!0),k(i,"".concat(o.class),o.class),k(i,"".concat(a.value,"-rtl"),g.value==="rtl"),i)}),d=G("fade");return f(Z,d,{default:function(){return[Q(f("div",T(T({},v),{},{ref:l}),[((u=r.default)===null||u===void 0?void 0:u.call(r))||p]),[[Y,m.visible]])]}})}}}),_t=H(Tt);const bt=$({name:"LayoutFeatures",components:{BackTop:_t,LayoutLockPage:I(()=>D(()=>import("./index.bec21aa3.js"),["assets/index.bec21aa3.js","assets/index.f8bcf808.js","assets/index.843606c8.css","assets/LockPage.f2137d90.js","assets/LockPage.6755d871.css","assets/lock.74a6de68.js","assets/header.d801b988.js"])),SettingDrawer:I(()=>D(()=>import("./index.a200aa1f.js").then(function(e){return e.i}),["assets/index.a200aa1f.js","assets/index.5c7227e9.css","assets/index.f8bcf808.js","assets/index.843606c8.css","assets/index.d3397da6.js","assets/index.5f1d9076.css","assets/FullscreenOutlined.f88b6f65.js","assets/index.ffdfd07f.js","assets/index.55076fdd.css","assets/useWindowSizeFn.d2a0a89b.js","assets/useContentViewHeight.0d100a60.js","assets/uniqBy.0ae55b98.js","assets/_baseIteratee.f49fbaac.js","assets/index.e344e4ac.js","assets/index.a2831ae3.css","assets/RedoOutlined.ed3d1686.js","assets/lock.74a6de68.js","assets/ArrowLeftOutlined.73a6b26e.js","assets/index.91cad2d2.js","assets/index.3a3c1369.css"])),SessionTimeoutLogin:ct},setup(){const{getUseOpenBackTop:e,getShowSettingButton:t,getSettingButtonPosition:n,getFullContent:r}=et(),o=nt(),{prefixCls:s}=rt("setting-drawer-feature"),{getShowHeader:c}=st(),a=L(()=>o.getSessionTimeout),g=L(()=>{if(!_(t))return!1;const l=_(n);return l===V.AUTO?!_(c)||_(r):l===V.FIXED});return{getTarget:()=>document.body,getUseOpenBackTop:e,getIsFixedSettingDrawer:g,prefixCls:s,getIsSessionTimeout:a}}});function St(e,t,n,r,o,s){const c=b("LayoutLockPage"),a=b("BackTop"),g=b("SettingDrawer"),l=b("SessionTimeoutLogin");return S(),ot(it,null,[f(c),e.getUseOpenBackTop?(S(),P(a,{key:0,target:e.getTarget},null,8,["target"])):C("",!0),e.getIsFixedSettingDrawer?(S(),P(g,{key:1,class:at(e.prefixCls)},null,8,["class"])):C("",!0),e.getIsSessionTimeout?(S(),P(l,{key:2})):C("",!0)],64)}var Mt=tt(bt,[["render",St]]);export{Mt as default};
