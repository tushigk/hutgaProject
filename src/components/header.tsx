"use client";
import { BellOutlined } from "@ant-design/icons";
import { Poppins } from "next/font/google";

const poppins = Poppins({
  weight: "400",
  subsets: ["latin"],
});

export function Header() {
  const userNavigation = [
    { name: "Your profile", href: "#" },
    { name: "Sign out", href: "#" },
  ];

  return (
    <header className="bg-white text-black w-[85%] h-20 shadow-md ml-[15%] flex justify-between items-center">
      <div className="flex ml-auto items-center space-x-4 mr-5">
        <div className="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300">
          <BellOutlined className="text-xl text-gray-700" />
        </div>

        <div className="flex items-center">
          <img
            src="/icon/user.png"
            alt="User Icon"
            className="w-10 h-10 rounded-2xl"
          />
          <div className="ml-3">
            <div className={`${poppins.className} text-sm`}>
              Admin@gmail.com
            </div>
            <div className={`${poppins.className} text-sm text-gray-400`}>
              UI designer
            </div>
          </div>
        </div>
      </div>
    </header>
  );
}
