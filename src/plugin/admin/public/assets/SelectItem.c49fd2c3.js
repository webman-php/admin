import{aG as i,a as l,aN as r,b as p,f as m,aI as d,o as c,h as u,i as f,t as g,j as _,b5 as b,n as v}from"./index.656c725e.js";import{b as y}from"./index.61b6a288.js";import"./index.81e1f965.js";import"./FullscreenOutlined.52703f8e.js";import"./index.3b25deff.js";import"./useWindowSizeFn.2783558b.js";import"./useContentViewHeight.8799f330.js";import"./uniqBy.73b96c75.js";import"./_baseIteratee.d7866646.js";import"./index.6010d68f.js";import"./RedoOutlined.b54223ae.js";import"./lock.344c64f4.js";import"./ArrowLeftOutlined.fc4e10b6.js";import"./index.4f3dd38a.js";const C=l({name:"SelectItem",components:{Select:r},props:{event:{type:Number},disabled:{type:Boolean},title:{type:String},def:{type:[String,Number]},initValue:{type:[String,Number]},options:{type:Array,default:()=>[]}},setup(e){const{prefixCls:t}=p("setting-select-item"),a=m(()=>e.def?{value:e.def,defaultValue:e.initValue||e.def}:{});function n(s){e.event&&y(e.event,s)}return{prefixCls:t,handleChange:n,getBindValue:a}}});function S(e,t,a,n,s,h){const o=d("Select");return c(),u("div",{class:v(e.prefixCls)},[f("span",null,g(e.title),1),_(o,b(e.getBindValue,{class:`${e.prefixCls}-select`,onChange:e.handleChange,disabled:e.disabled,size:"small",options:e.options}),null,16,["class","onChange","disabled","options"])],2)}var q=i(C,[["render",S],["__scopeId","data-v-6707e46b"]]);export{q as default};