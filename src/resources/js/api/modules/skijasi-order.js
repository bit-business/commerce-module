import resource from "../../../../../../core/src/resources/js/api/resource";
import QueryString from "../../../../../../core/src/resources/js/api/query-string";

let apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX + "/module/commerce"
  : "/skijasi-api/module/commerce";

export default {
  browse(data = {}) {
    let ep = apiPrefix + "/v1/order";
    let qs = QueryString(data);
    let url = ep + qs;
    return resource.get(url);
  },


  deleteOrderDetail(data) {
    let ep = apiPrefix + "/v1/order/delete-order-detail";
    return resource.post(ep, data);
  },
  
  // Update the read method to include deleted items
  read(data) {
    let ep = apiPrefix + "/v1/order/read";
    let qs = QueryString(data);
    let url = ep + qs;
    return resource.get(url);
  },

  confirm(data) {
    return resource.post(apiPrefix + "/v1/order/confirm", data);
  },

  reject(data) {
    return resource.post(apiPrefix + "/v1/order/reject", data);
  },

  ship(data) {
    return resource.post(apiPrefix + "/v1/order/ship", data);
  },

  done(data) {
    return resource.post(apiPrefix + "/v1/order/done", data);
  },


  getOrdersPerMonth(data = {}) {
    const ep = `${apiPrefix}/v1/order/orderpermonth`;
    const qs = QueryString(data);
    const url = `${ep}${qs}`;
    return resource.get(url);
  },

  generatePaymentSlip(data) {
    return resource.post(`${apiPrefix}/v1/order/public/stvoriuplatnicu`, data);
  },



  getTotalCompletedOrders() {
    let url = apiPrefix + "/v1/order/public/gettotalcompletedorders";
    return resource.get(url);
  },

  totalneworders() {
    let url = apiPrefix + "/v1/order/totalneworders";
    return resource.get(url);
  },


  copyToShipping(data) {
    return resource.post(`${apiPrefix}/v1/order/copy-to-shipping`, data);
  },

  
};
