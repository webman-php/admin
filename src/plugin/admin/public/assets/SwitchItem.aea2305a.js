import{aG as r,a as d,b as l,f as p,aI as c,o as m,h,i as u,t as f,j as C,b5 as g,n as _,c as v}from"./index.656c725e.js";import{S as b}from"./index.2e5a1dae.js";import{b as y}from"./index.61b6a288.js";import"./index.81e1f965.js";import"./FullscreenOutlined.52703f8e.js";import"./index.3b25deff.js";import"./useWindowSizeFn.2783558b.js";import"./useContentViewHeight.8799f330.js";import"./uniqBy.73b96c75.js";import"./_baseIteratee.d7866646.js";import"./index.6010d68f.js";import"./RedoOutlined.b54223ae.js";import"./lock.344c64f4.js";import"./ArrowLeftOutlined.fc4e10b6.js";import"./index.4f3dd38a.js";const S=d({name:"SwitchItem",components:{Switch:b},props:{event:{type:Number},disabled:{type:Boolean},title:{type:String},def:{type:Boolean}},setup(e){const{prefixCls:t}=l("setting-switch-item"),{t:n}=v(),o=p(()=>e.def?{checked:e.def}:{});function a(i){e.event&&y(e.event,i)}return{prefixCls:t,t:n,handleChange:a,getBindValue:o}}});function k(e,t,n,o,a,i){const s=c("Switch");return m(),h("div",{class:_(e.prefixCls)},[u("span",null,f(e.title),1),C(s,g(e.getBindValue,{onChange:e.handleChange,disabled:e.disabled,checkedChildren:e.t("layout.setting.on"),unCheckedChildren:e.t("layout.setting.off")}),null,16,["onChange","disabled","checkedChildren","unCheckedChildren"])],2)}var F=r(S,[["render",k],["__scopeId","data-v-440e72fd"]]);export{F as default};