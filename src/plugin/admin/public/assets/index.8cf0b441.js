import{a2 as W,a3 as F,a as j,U as S,f as k,T as E,K as d,j as s,cO as H,cP as K,r as Q,F as V,cQ as q,aa as N,aH as z}from"./index.f8bcf808.js";function ee(l){var e=W();return F(function(){e.value=l()},{flush:"sync"}),e}var G=function(){return{prefixCls:String,checked:{type:Boolean,default:void 0},onChange:{type:Function},onClick:{type:Function},"onUpdate:checked":Function}},J=j({compatConfig:{MODE:3},name:"ACheckableTag",props:G(),setup:function(e,r){var o=r.slots,i=r.emit,g=S("tag",e),u=g.prefixCls,c=function(C){var v=e.checked;i("update:checked",!v),i("change",!v),i("click",C)},p=k(function(){var n;return E(u.value,(n={},d(n,"".concat(u.value,"-checkable"),!0),d(n,"".concat(u.value,"-checkable-checked"),e.checked),n))});return function(){var n;return s("span",{class:p.value,onClick:c},[(n=o.default)===null||n===void 0?void 0:n.call(o)])}}}),m=J,L=new RegExp("^(".concat(H.join("|"),")(-inverse)?$")),X=new RegExp("^(".concat(K.join("|"),")$")),Y=function(){return{prefixCls:String,color:{type:String},closable:{type:Boolean,default:!1},closeIcon:N.any,visible:{type:Boolean,default:void 0},onClose:{type:Function},"onUpdate:visible":Function,icon:N.any}},f=j({compatConfig:{MODE:3},name:"ATag",props:Y(),slots:["closeIcon","icon"],setup:function(e,r){var o=r.slots,i=r.emit,g=r.attrs,u=S("tag",e),c=u.prefixCls,p=u.direction,n=Q(!0);F(function(){e.visible!==void 0&&(n.value=e.visible)});var C=function(t){t.stopPropagation(),i("update:visible",!1),i("close",t),!t.defaultPrevented&&e.visible===void 0&&(n.value=!1)},v=k(function(){var a=e.color;return a?L.test(a)||X.test(a):!1}),R=k(function(){var a;return E(c.value,(a={},d(a,"".concat(c.value,"-").concat(e.color),v.value),d(a,"".concat(c.value,"-has-color"),e.color&&!v.value),d(a,"".concat(c.value,"-hidden"),!n.value),d(a,"".concat(c.value,"-rtl"),p.value==="rtl"),a))});return function(){var a,t,h,b=e.icon,w=b===void 0?(a=o.icon)===null||a===void 0?void 0:a.call(o):b,y=e.color,P=e.closeIcon,T=P===void 0?(t=o.closeIcon)===null||t===void 0?void 0:t.call(o):P,x=e.closable,O=x===void 0?!1:x,B=function(){return O?T?s("span",{class:"".concat(c.value,"-close-icon"),onClick:C},[T]):s(z,{class:"".concat(c.value,"-close-icon"),onClick:C},null):null},U={backgroundColor:y&&!v.value?y:void 0},I=w||null,$=(h=o.default)===null||h===void 0?void 0:h.call(o),A=I?s(V,null,[I,s("span",null,[$])]):$,D="onClick"in g,_=s("span",{class:R.value,style:U},[A,B()]);return D?s(q,null,{default:function(){return[_]}}):_}}});f.CheckableTag=m;f.install=function(l){return l.component(f.name,f),l.component(m.name,m),l};var ae=f;export{ae as T,ee as e};
