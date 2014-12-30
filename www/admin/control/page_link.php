<?php
$update=false;
include_once PATH_HANDLER . 'PageHandler.php';
$page = new PageHandler ( $uid );
$ret = $page->getLinkList ();

$pagelist=$page->getPageList(1);

foreach ($ret as $key=>$value){
	$ret[$key]['html']=getSelect('select_'.$value['_id'],$pagelist,$value['link']);	
}




function getSelect($htmlid,$data,$select=''){
	$head="<select  style='width:100px' id='$htmlid'><option value=''>未选择</option>";
	foreach ($data as $key=>$value){
		if($select==$value['_id'])
		{
			$strz.="<option value='".$value['_id']."' selected>".$value['title']."</option>";
		}else{
			$strz.="<option value='".$value['_id']."'>".$value['title']."</option>";
		}
			
	}
	$foot="</select>";
	return $head.$strz.$foot;
}
$select="<select name='sldd' style='width:58px' onchange='location.href=this.options[this.selectedIndex].value;'>
<option value='page_1.html' selected>1</option>
<option value='page_2.html'>2</option>
<option value='page_3.html'>3</option>
<option value='page_4.html'>4</option>
<option value='page_5.html'>5</option>
<option value='page_6.html'>6</option>
<option value='page_7.html'>7</option>
<option value='page_8.html'>8</option>
<option value='page_9.html'>9</option>
<option value='page_10.html'>10</option>
<option value='page_11.html'>11</option>
<option value='page_12.html'>12</option>
<option value='page_13.html'>13</option>
<option value='page_14.html'>14</option>
<option value='page_15.html'>15</option>
<option value='page_16.html'>16</option>
<option value='page_17.html'>17</option>
<option value='page_18.html'>18</option>
<option value='page_19.html'>19</option>
<option value='page_20.html'>20</option>
<option value='page_21.html'>21</option>
<option value='page_22.html'>22</option>
<option value='page_23.html'>23</option>
<option value='page_24.html'>24</option>
<option value='page_25.html'>25</option>
<option value='page_26.html'>26</option>
<option value='page_27.html'>27</option>
<option value='page_28.html'>28</option>
<option value='page_29.html'>29</option>
<option value='page_30.html'>30</option>
<option value='page_31.html'>31</option>
<option value='page_32.html'>32</option>
<option value='page_33.html'>33</option>
<option value='page_34.html'>34</option>
<option value='page_35.html'>35</option>
<option value='page_36.html'>36</option>
<option value='page_37.html'>37</option>
<option value='page_38.html'>38</option>
<option value='page_39.html'>39</option>
<option value='page_40.html'>40</option>
<option value='page_41.html'>41</option>
<option value='page_42.html'>42</option>
<option value='page_43.html'>43</option>
<option value='page_44.html'>44</option>
<option value='page_45.html'>45</option>
<option value='page_46.html'>46</option>
<option value='page_47.html'>47</option>
<option value='page_48.html'>48</option>
<option value='page_49.html'>49</option>
<option value='page_50.html'>50</option>
<option value='page_51.html'>51</option>
<option value='page_52.html'>52</option>
<option value='page_53.html'>53</option>
<option value='page_54.html'>54</option>
<option value='page_55.html'>55</option>
<option value='page_56.html'>56</option>
<option value='page_57.html'>57</option>
<option value='page_58.html'>58</option>
<option value='page_59.html'>59</option>
<option value='page_60.html'>60</option>
<option value='page_61.html'>61</option>
<option value='page_62.html'>62</option>
<option value='page_63.html'>63</option>
<option value='page_64.html'>64</option>
<option value='page_65.html'>65</option>
<option value='page_66.html'>66</option>
<option value='page_67.html'>67</option>
<option value='page_68.html'>68</option>
<option value='page_69.html'>69</option>
<option value='page_70.html'>70</option>
<option value='page_71.html'>71</option>
<option value='page_72.html'>72</option>
<option value='page_73.html'>73</option>
<option value='page_74.html'>74</option>
<option value='page_75.html'>75</option>
<option value='page_76.html'>76</option>
<option value='page_77.html'>77</option>
<option value='page_78.html'>78</option>
<option value='page_79.html'>79</option>
<option value='page_80.html'>80</option>
<option value='page_81.html'>81</option>
<option value='page_82.html'>82</option>
<option value='page_83.html'>83</option>
<option value='page_84.html'>84</option>
<option value='page_85.html'>85</option>
<option value='page_86.html'>86</option>
<option value='page_87.html'>87</option>
<option value='page_88.html'>88</option>
<option value='page_89.html'>89</option>
<option value='page_90.html'>90</option>
<option value='page_91.html'>91</option>
<option value='page_92.html'>92</option>
<option value='page_93.html'>93</option>
<option value='page_94.html'>94</option>
<option value='page_95.html'>95</option>
<option value='page_96.html'>96</option>
<option value='page_97.html'>97</option>
<option value='page_98.html'>98</option>
<option value='page_99.html'>99</option>
</select>";