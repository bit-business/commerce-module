import resource from "../../../../../../core/src/resources/js/api/resource";
import QueryString from "../../../../../../core/src/resources/js/api/query-string";

let apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX + "/module/commerce"
  : "/skijasi-api/module/commerce";

export default {
  add(data) {
    return resource.post(apiPrefix + "/v1/product-detail/add", data);
  },

  edit(data) {
    return resource.put(apiPrefix + "/v1/product-detail/edit", data);
  },

  delete(data) {
    let paramData = {
      data: data,
    };
    return resource.delete(apiPrefix + "/v1/product-detail/delete", paramData);
  },

  browse(data = {}) {
    let ep = apiPrefix + "/v1/product-detail/browse";
    let qs = QueryString(data);
    let url = ep + qs;
    return resource.get(url);
  },
};
