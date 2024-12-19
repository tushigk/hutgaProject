import Login from "./(public)/login/page";
import "../styles/globals.css";

export default function Main() {
  return (
    <div
      className="flex items-center justify-center min-h-screen relative"
      style={{
        backgroundImage: "url('/bg.png')",
        backgroundSize: "cover",
        backgroundPosition: "center",
      }}
    >
      <div className="w-[450px] mx-auto p-[40px] bg-white shadow-md rounded-xl h-[450px]">
        <div className="flex items-center justify-center mb-[20px]">
          <img src="/black.svg" alt="Logo" className="h-12" />
        </div>
        <Login />
      </div>
    </div>
  );
}
