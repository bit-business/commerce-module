import resource from "../../../../../../core/src/resources/js/api/resource";
import QueryString from "../../../../../../core/src/resources/js/api/query-string";

let apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX + "/module/commerce"
  : "/skijasi-api/module/commerce";

export default {
  browse(data = {}) {
    let ep = apiPrefix + "/v1/cart";
    let qs = QueryString(data);
    let url = ep + qs;
    return resource.get(url);
  },

  read(data) {
    let ep = apiPrefix + "/v1/cart/read";
    let qs = QueryString(data);
    let url = ep + qs;
    return resource.get(url);
  },
  // delete(data) {
  //   let paramData = {
  //     data: data,
  //   };
  //   return resource.delete(apiPrefix + "/v1/cart/delete", paramData);
  // },  dobio api success greske
  delete(data) {
    return resource.post(apiPrefix + "/v1/cart/delete", { 
      _method: 'DELETE',
      id: data
    });
  },



  edit(data) {
    return resource.put(apiPrefix + "/v1/cart/edit", data);
  },



};
