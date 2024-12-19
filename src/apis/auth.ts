import { store } from "@/store";
import { authMe, setToken } from "@/store/auth-slice";
import { HttpRequest } from "@/utils/request";

const appHttpRequest = new HttpRequest(null, `https://localhost:3000`);

export const me = async () => {
  try {
    const res = await appHttpRequest.get("/me");
    store.dispatch(authMe(res));
    return res?.user || res;
  } catch (err) {
    throw err;
  }
};

export const login = async (data: { phone: string; password: string }) => {
  const res = await appHttpRequest.post("/login", data);
  store.dispatch(setToken(res));
  return res;
};
