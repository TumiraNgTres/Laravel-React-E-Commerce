import { Link, usePage } from "@inertiajs/react";

function DepartmentNavBar() {
  const { departments } = usePage().props; // get auth from inertia props

  return (
    <div className="navbar bg-base-100 border-t min-h-4">
      <div className="navbar-center hidden lg:flex">
        <ul className="menu menu-horizontal menu-dropdown dropdown-hover px-1 z-20 py-0">
          {departments.map((department) => (
            <li key={department.id}>
              <Link href={route("product.byDepartment", department.slug)}>
                {department.name}
              </Link>
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
}

export default DepartmentNavBar;
