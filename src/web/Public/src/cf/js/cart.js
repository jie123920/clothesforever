//cart
jQuery(function() {

	// preview
	$('.spec-n2').delegate('li','click', function() {
		//获取图片的地址
		// var dataSrc = $(this).find('a').data('anchor-id');
        var dataSrc = $(this).find('img')[0].src;
        console.log(dataSrc)
		$('#spec-img').attr('src', dataSrc);
		$(this).addClass('tb-selected').siblings().removeClass('tb-selected');
	})


	//

	// $('#add-cart').on('click',function(event){
	// 	event.preventDefault()
	// 	$('.bone-box').eq(0).show();
	// })

	$('.box-del').on('click', function() {
		$(this).parents('.bone-box').hide();
	})

	var inpVal = $('#buy-num');
	$('.cart-compute .cf-reduce').on('click', function() {
		if (inpVal.val() > 1) {
			inpVal.val(parseInt(inpVal.val()) - 1)
		}
	})

	$('.cart-compute .cf-add').on('click', function() {
		inpVal.val(parseInt(inpVal.val()) + 1)
	})

	$('#buy-num').change(function() {

		var re = /^\\d+(\\.\\d+)$/; //浮点数

		if (isNaN(inpVal.val()) || inpVal.val() < 0 || inpVal.val() == '') {
			$('#buy-num').val(1)
		} else if (!re.test(inpVal.val())) {
			$('#buy-num').val(parseInt(inpVal.val()))
		} else if (inpVal.val() > 100) {
			alert('不能多于100')
		}
	})


	// goods-news show

	$('.cart-info .goods-news-title').on('click', function() {

		$(this).siblings('.goods-intro').toggleClass('c-active');

		if ($(this).siblings('.goods-intro').hasClass('c-active')) {
			$(this).find('i').removeClass('fa-plus').addClass('fa-minus');
            $(this).css('border','none');
		} else {
			$(this).find('i').removeClass('fa-minus').addClass('fa-plus');
            $(this).css('border-bottom','1px solid #af9e89');
		}

		$(this).parent('li').siblings().find('.goods-intro').removeClass('c-active');

		if ($(this).parent('li').siblings().children().hasClass('goods-intro')) {
			$(this).parent('li').siblings().find('i').removeClass('fa-minus').addClass('fa-plus')
		} else {
			$(this).find('i').removeClass('fa-plus').addClass('fa-minus')
		}
	})



	// 点击添加到购物车


	// 点击到购买	



	// 后端数据
	/**
	 *  red yellow white blue ==> 10 11 12 13
	 *  size ==> 20 21 22 23
	 **/
	var data = window.goodDatas;

	//var startTime = new Date().getTime();

	//保存最后的组合结果信息
	window.SKUResult = {};
	//获得对象的key
	function getObjKeys(obj) {
		if (obj !== Object(obj)) {
			throw new TypeError('Invalid object');
		}
		var keys = [];
		for (var key in obj) {
			if (Object.prototype.hasOwnProperty.call(obj, key)) {
				keys[keys.length] = key;
			}
		}
		return keys;
	}

	//把组合的key放入结果集SKUResult
	function add2SKUResult(combArrItem, sku) {
		var key = combArrItem.join(";");
		if (SKUResult[key]) { //SKU信息key属性·
			SKUResult[key].count += sku.count;
			SKUResult[key].prices.push(sku.price);
		} else {
			SKUResult[key] = {
				count: sku.count,
				prices: [sku.price],
				photos:sku.photos
			};
		}
	}

	//初始化得到结果集
	function initSKU() {
		var i, j, skuKeys = getObjKeys(data);
		for (i = 0; i < skuKeys.length; i++) {
			var skuKey = skuKeys[i]; //一条SKU信息key
			var sku = data[skuKey]; //一条SKU信息value
			var skuKeyAttrs = skuKey.split(";"); //SKU信息key属性值数组
			skuKeyAttrs.sort(function(value1, value2) {
				return parseInt(value1) - parseInt(value2);
			});

			//对每个SKU信息key属性值进行拆分组合
			var combArr = combInArray(skuKeyAttrs);
			for (j = 0; j < combArr.length; j++) {
				add2SKUResult(combArr[j], sku);
			}

			//结果集接放入SKUResult
			SKUResult[skuKeyAttrs.join(";")] = {
				count: sku.count,
				prices: [sku.price],
				photos: sku.photos
			}
		}
	}

	/**
	 * 从数组中生成指定长度的组合
	 * 方法: 先生成[0,1...]形式的数组, 然后根据0,1从原数组取元素，得到组合数组
	 */
	function combInArray(aData) {
		if (!aData || !aData.length) {
			return [];
		}

		var len = aData.length;
		var aResult = [];

		for (var n = 1; n < len; n++) {
			var aaFlags = getCombFlags(len, n);
			while (aaFlags.length) {
				var aFlag = aaFlags.shift();
				var aComb = [];
				for (var i = 0; i < len; i++) {
					aFlag[i] && aComb.push(aData[i]);
				}
				aResult.push(aComb);
			}
		}

		return aResult;
	}

	/**
	 * 得到从 m 元素中取 n 元素的所有组合
	 * 结果为[0,1...]形式的数组, 1表示选中，0表示不选
	 */
	function getCombFlags(m, n) {
		if (!n || n < 1) {
			return [];
		}

		var aResult = [];
		var aFlag = [];
		var bNext = true;
		var i, j, iCnt1;

		for (i = 0; i < m; i++) {
			aFlag[i] = i < n ? 1 : 0;
		}

		aResult.push(aFlag.concat());

		while (bNext) {
			iCnt1 = 0;
			for (i = 0; i < m - 1; i++) {
				if (aFlag[i] == 1 && aFlag[i + 1] == 0) {
					for (j = 0; j < i; j++) {
						aFlag[j] = j < iCnt1 ? 1 : 0;
					}
					aFlag[i] = 0;
					aFlag[i + 1] = 1;
					var aTmp = aFlag.concat();
					aResult.push(aTmp);
					if (aTmp.slice(-n).join("").indexOf('0') == -1) {
						bNext = false;
					}
					break;
				}
				aFlag[i] == 1 && iCnt1++;
			}
		}
		return aResult;
	}



	var app = {
		init: function() {
			initSKU();
			var $skuWRAP = $('.choose-attrs');
			var $skuWRAPChild = $skuWRAP.find('.sku'); //所有sku对象

			$skuWRAPChild.each(function() {
				//加载页面遍历获取哪些应该变灰掉
				var self = $(this);
				var attr_id = self.attr('data-attrid');
				if (!SKUResult[attr_id]) {
					self.addClass('attr-disabled');
				}
			}).on('click', function() {
				var self = $(this);
				if (self.hasClass('attr-disabled')) {
					return false;
				}

				//选中自己，兄弟节点取消选中
				self.toggleClass('sku-selected').siblings().removeClass('sku-selected');

				//已经选择的节点
				var selectedObjs = $skuWRAP.find('.sku-selected');

				if (selectedObjs.length) {

					//获得组合key价格
					var selectedIds = [];
					selectedObjs.each(function() {
						selectedIds.push($(this).attr('data-attrid'));
					});

					//将结果排序
					selectedIds.sort(function(value1, value2) {
						return parseInt(value1) - parseInt(value2);
					});

					var len = selectedIds.length;
					var prices = SKUResult[selectedIds.join(';')].prices;
					var photos = SKUResult[selectedIds.join(';')].photos;
					var maxPrice = Math.max.apply(Math, prices);
					var minPrice = Math.min.apply(Math, prices);
					$('.attr-price').text(maxPrice > minPrice ? minPrice + "-" + maxPrice : maxPrice); //获取价格
					self.closest('dl').find('.attrs-name').text($(this).data('value'))
                    //循环的把图片加入列表
					//alert(JSON.stringify(SKUResult[selectedIds.join(';')]));
					if(photos.length >0){
						var photos_str = '';
						for(var i=0;i<photos.length;i++){
							photos_str = photos_str +'<li><a href="javascript:;"><img src ="'+photos[i]+'" title="" alt=""></a></li>';
							if(i==0){
								$('.spec-n1').html('<img id="spec-img" src ="'+photos[i]+'" title="" alt="">');
							}
						}
						$('.spec-n2').html(photos_str)
						$('.spec-n2 li').eq(0).addClass("tb-selected")
					}
					//用已选中的节点验证待测试节点 underTestObjs
					$skuWRAPChild.not(selectedObjs).not(self).each(function() {
						var siblingsSelectedObj = $(this).siblings('.sku-selected');
						var testAttrIds = []; //从选中节点中去掉选中的兄弟节点
						if (siblingsSelectedObj.length) {
							var siblingsSelectedObjId = siblingsSelectedObj.attr('data-attrid');
							for (var i = 0; i < len; i++) {
								(selectedIds[i] != siblingsSelectedObjId) && testAttrIds.push(selectedIds[i]);
							}
						} else {
							testAttrIds = selectedIds.concat();
						}
						testAttrIds = testAttrIds.concat($(this).attr('data-attrid'));
						testAttrIds.sort(function(value1, value2) {
							return parseInt(value1) - parseInt(value2);
						});

						if (!SKUResult[testAttrIds.join(';')]) {
							$(this).addClass('attr-disabled').removeClass('sku-selected');
						} else {
							if (SKUResult[testAttrIds.join(';')].count > 0) {
								$(this).removeClass('attr-disabled');
							} else {
								$(this).addClass('attr-disabled').removeClass('sku-selected');
							}
						}
					});
				} else {
					//设置默认价格
					//$('.attr-price').text('50');
					//设置属性状态
					$skuWRAPChild.each(function() {
						SKUResult[$(this).attr('data-attrid')] ? $(this).removeClass('attr-disabled') : $(this).addClass('attr-disabled').removeClass('sku-selected');
					})
				}
			});
		}
	};

	app.init()

	/*============以下代码另作他用===============*/
	//获取 key的库存量
	var myData = {};
	//这个是获取数量的跟本次无关
	function getNum(key) {
		var result = 0,

			i, j, m,

			items, n = [];

		//检查是否已计算过
		if (typeof myData[key] != 'undefined') {
			return myData[key];
		}

		items = key.split(";");

		//已选择数据是最小路径，直接从后端数据获取
		if (items.length === keys.length) {
			return data[key] ? data[key].count : 0;
		}

		//拼接子串
		for (i = 0; i < keys.length; i++) {
			for (j = 0; j < keys[i].length && items.length > 0; j++) {
				if (keys[i][j] == items[0]) {
					break;
				}
			}

			if (j < keys[i].length && items.length > 0) {
				//找到该项，跳过
				n.push(items.shift());
			} else {
				//分解求值
				for (m = 0; m < keys[i].length; m++) {
					result += getNum(n.concat(keys[i][m], items).join(";"));
				}
				break;
			}
		}

		//缓存
		myData[key] = result;
		return result;
	}
})