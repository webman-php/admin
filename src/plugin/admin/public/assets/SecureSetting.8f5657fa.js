var w=(e,r,o)=>new Promise((s,c)=>{var a=i=>{try{t(o.next(i))}catch(p){c(p)}},n=i=>{try{t(o.throw(i))}catch(p){c(p)}},t=i=>i.done?s(i.value):Promise.resolve(i.value).then(a,n);t((o=o.apply(e,r)).next())});import{B as S}from"./BasicForm.1fff6f7d.js";import{a as C,U as x,K as f,bb as m,j as l,aa as d,f as I,aJ as D,r as G,I as P,cp as A,aL as b,o as E,h as $,p as j,q as k,eW as q,x as N}from"./index.02bbedaf.js";import{C as u}from"./index.a3b404ad.js";import"./index.cef58315.js";import"./index.9a84248c.js";import"./index.47561936.js";import"./_baseIteratee.2b89f8d1.js";import"./index.441385e5.js";import"./index.2a917be4.js";import"./index.5be6cedd.js";import"./index.29c90a5d.js";import"./index.d5cdfa6a.js";import"./index.125d35f2.js";import"./index.00a4dc86.js";import"./index.aafb2f3b.js";import"./useWindowSizeFn.c5b50549.js";import"./FullscreenOutlined.a5f55a02.js";import"./uniqBy.e456814b.js";import"./download.d180e438.js";import"./index.4792e25b.js";var R=function(){return{prefixCls:String,title:d.any,description:d.any,avatar:d.any}},v=C({compatConfig:{MODE:3},name:"ACardMeta",props:R(),slots:["title","description","avatar"],setup:function(r,o){var s=o.slots,c=x("card",r),a=c.prefixCls;return function(){var n=f({},"".concat(a.value,"-meta"),!0),t=m(s,r,"avatar"),i=m(s,r,"title"),p=m(s,r,"description"),y=t?l("div",{class:"".concat(a.value,"-meta-avatar")},[t]):null,g=i?l("div",{class:"".concat(a.value,"-meta-title")},[i]):null,h=p?l("div",{class:"".concat(a.value,"-meta-description")},[p]):null,M=g||h?l("div",{class:"".concat(a.value,"-meta-detail")},[g,h]):null;return l("div",{class:n},[y,M])}}}),T=function(){return{prefixCls:String,hoverable:{type:Boolean,default:!0}}},_=C({compatConfig:{MODE:3},name:"ACardGrid",__ANT_CARD_GRID:!0,props:T(),setup:function(r,o){var s=o.slots,c=x("card",r),a=c.prefixCls,n=I(function(){var t;return t={},f(t,"".concat(a.value,"-grid"),!0),f(t,"".concat(a.value,"-grid-hoverable"),r.hoverable),t});return function(){var t;return l("div",{class:n.value},[(t=s.default)===null||t===void 0?void 0:t.call(s)])}}});u.Meta=v;u.Grid=_;u.install=function(e){return e.component(u.name,u),e.component(v.name,v),e.component(_.name,_),e};const B=G(null),F=C({components:{BasicForm:S,[P.name]:P,Button:A,Card:u},emits:["reload","register"],setup(){const e=[{field:"old_password",component:"Input",label:"original password",colProps:{span:20},required:!0},{field:"password",component:"Input",label:"new password",colProps:{span:20},required:!0},{field:"password_confirm",component:"Input",label:"Confirm Password",colProps:{span:20},required:!0}],{createMessage:r}=N(),{success:o,error:s}=r;return{formElRef:B,handleSubmit:()=>w(this,null,function*(){try{const a=B.value;if(!a)return;const n=yield a.validate();if(n.password!=n.password_confirm){s("Two times of password input are inconsistent");return}yield q(n),o("Successful operation")}catch(a){console.log(a)}}),schemas:e}}}),U={class:"mt-3"};function O(e,r,o,s,c,a){const n=b("BasicForm"),t=b("Button");return E(),$("div",U,[l(n,{schemas:e.schemas,ref:"formElRef",labelWidth:75,showActionButtonGroup:!1},null,8,["schemas"]),l(t,{type:"primary",onClick:e.handleSubmit},{default:j(()=>[k(" Update Password ")]),_:1},8,["onClick"])])}var pt=D(F,[["render",O]]);export{pt as default};
