"use client";
import {
  HomeIcon,
  UsersIcon,
  CogIcon,
  BanknotesIcon,
  CalculatorIcon,
  BuildingOfficeIcon,
  CircleStackIcon,
  ScaleIcon,
  TruckIcon,
} from "@heroicons/react/24/outline";
import { useRouter } from "next/navigation";
import { usePathname } from "next/navigation";
import Link from "next/link";
export function Navbar() {
  const router = useRouter();
  const pathname = usePathname();

  function classNames(...classes: string[]) {
    return classes.filter(Boolean).join(" ");
  }

  const navigation = [
    { name: "Дашбоард", path: "/home", icon: HomeIcon },
    { name: "Ажилтан", path: "/employee", icon: UsersIcon },
    {
      name: "Харилцагч байгууллага",
      path: "/organization",
      icon: BuildingOfficeIcon,
    },
    { name: "Дамжлагын бүртгэл", path: "/course", icon: CogIcon },
    { name: "Хатгамал", path: "/", icon: BanknotesIcon },
    { name: "Эцсийн дамжлага", path: "/", icon: ScaleIcon },
    { name: "Бэлэн бараа", path: "/product", icon: CircleStackIcon },
    { name: "Кластер захиалга", path: "/", icon: TruckIcon },
    { name: "Цалин", path: "/salary", icon: CalculatorIcon },
    { name: "БМ бүртгэл", path: "/", icon: UsersIcon },
  ];

  return (
    <div>
      <div className="fixed inset-y-0 z-50 flex w-[15%] shadow-lg">
        <div className="flex grow flex-col gap-y-4 overflow-y-auto px-6 pb-4 bg-[#171829]">
          <div className="flex place-items-start py-4 border-b border-gray-700">
            <img src="/logo.svg" alt="Logo" className="h-12" />
          </div>
          <nav className="flex flex-1 flex-col">
            <ul>
              <li>
                <ul className="-mx-2 space-y-1">
                  {navigation.map((item) => (
                    <li key={item.name}>
                      <Link
                        href={item.path}
                        className={classNames(
                          pathname === item.path
                            ? "bg-blue-700 border-blue-600 text-white"
                            : "text-gray-400 hover:bg-blue-600 hover:text-white",
                          "group flex gap-x-3 rounded-md p-3 text-sm font-semibold leading-6 transition-colors duration-300 ease-in-out cursor-pointer border border-transparent"
                        )}
                      >
                        <item.icon
                          aria-hidden="true"
                          className="h-6 w-6 shrink-0 text-gray-300 group-hover:text-white"
                        />
                        {item.name}
                      </Link>
                    </li>
                  ))}
                </ul>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  );
}
