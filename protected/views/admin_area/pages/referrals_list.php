<button class="btn btn-warning disable-all-filters" data-href="referrals">Сбросить фильтр</button>
<? $this->renderPartial("/admin_area/pages/_referrals_list", ['dataProvider' => $dataProvider]); ?>