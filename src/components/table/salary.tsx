"use client";
import {
  Table,
  Card,
  Button,
  Spin,
  Tag,
  Alert,
  Modal,
  Divider,
  Input,
  Form,
  Select,
  DatePicker,
} from "antd";
import {
  PlusOutlined,
  DeleteOutlined,
  EyeOutlined,
  EditOutlined,
} from "@ant-design/icons";
import type { ColumnsType } from "antd/es/table";
import { useState } from "react";

interface Item {
  _id: string;
  number: number;
  firstname: string;
  lastname: string;
  date: string;
  totalDays: number;
  workedDays: number;
  salary: number;
}

const SalaryTable = () => {
  const [selectedEmployee, setSelectedEmployee] = useState<Item | null>(null);
  const [isModalVisible, setIsModalVisible] = useState(false);
  const [isAddModalVisible, setIsAddModalVisible] = useState(false);
  const [isEditModalVisible, setIsEditModalVisible] = useState(false);
  const [editItem, setEditItem] = useState<Item | null>(null);
  const [showBaseSalaryInput, setShowBaseSalaryInput] = useState(false);

  const data: Item[] = [
    {
      _id: "1",
      number: 1,
      firstname: "Лутбат",
      lastname: "Түшиг",
      date: "2024-12-18",
      totalDays: 20,
      workedDays: 10,
      salary: 100000,
    },
  ];

  const columns: ColumnsType<Item> = [
    {
      title: "№",
      dataIndex: "number",
      key: "number",
    },
    {
      title: "Овог",
      dataIndex: "firstname",
      key: "firstname",
    },
    {
      title: "Нэр",
      dataIndex: "lastname",
      key: "lastname",
    },
    {
      title: "Бодогдсон сар",
      dataIndex: "date",
      key: "date",
    },
    {
      title: "Нийт хоног",
      dataIndex: "totalDays",
      key: "totalDays",
    },
    {
      title: "Ажилласан хоног",
      dataIndex: "workedDays",
      key: "workedDays",
    },
    {
      title: "Үндсэн цалин",
      dataIndex: "salary",
      key: "salary",
    },
    {
      title: "Үйлдэл",
      key: "action",
      render: (_, record: Item) => (
        <div className="flex justify-around">
          <Button
            type="link"
            icon={<EyeOutlined />}
            className="hover:text-blue-600"
            onClick={() => handleViewClick(record)}
          />
          <Button
            type="link"
            icon={<EditOutlined />}
            className="hover:text-blue-600"
            onClick={() => handleEditClick(record)}
          />
          <Button
            type="link"
            danger
            icon={<DeleteOutlined />}
            className="hover:text-red-600"
          />
        </div>
      ),
      width: 160,
      align: "center",
    },
  ];

  const handleAddClick = () => {
    setIsAddModalVisible(true);
  };

  const handleAddModalClose = () => {
    setIsAddModalVisible(false);
  };

  const handleAddSubmit = (values: any) => {
    console.log("New Employee:", values);
    setIsAddModalVisible(false);
  };

  const handleViewClick = (record: Item) => {
    setSelectedEmployee(record);
    setIsModalVisible(true);
  };

  const handleModalClose = () => {
    setIsModalVisible(false);
    setSelectedEmployee(null);
  };

  const renderFilterForm = () => {
    return (
      <Form layout="inline" className="mb-4">
        <Form.Item>
          <Select placeholder="Ажилтан нэр" />
        </Form.Item>
        <Form.Item className="w-1/5">
          <DatePicker placeholder="Бодогдсон сар " />
        </Form.Item>
      </Form>
    );
  };

  const handleEditClick = (record: Item) => {
    setEditItem(record);
    setIsEditModalVisible(true);
  };

  const handleEditModalClose = () => {
    setIsEditModalVisible(false);
    setEditItem(null);
  };

  const handleEditSubmit = (values: any) => {
    console.log("Edited Employee:", values);
    setIsEditModalVisible(false);
  };

  return (
    <div className="flex flex-col w-full p-4">
      <Card
        bordered={false}
        title="Цалингийн бүртгэл"
        extra={
          <Button
            icon={<PlusOutlined />}
            type="primary"
            onClick={handleAddClick}
          >
            Цалин хөлс нэмэх
          </Button>
        }
      >
        {renderFilterForm()}
        <Alert
          message={<p className="mb-0">Нийт тоо: {data.length}</p>}
          type="info"
          className="mb-4"
        />
        <Spin spinning={false}>
          <Table
            dataSource={data}
            columns={columns}
            rowKey={(record) => record._id}
            pagination={false}
          />
        </Spin>
      </Card>

      <Modal
        title="Цалин хөлс нэмэх"
        visible={isAddModalVisible}
        onCancel={handleAddModalClose}
        footer={null}
      >
        <Divider />
        <Form layout="vertical" onFinish={handleAddSubmit}>
          <Form.Item
            label="Ажилтны нэр"
            name="lastname"
            rules={[{ required: false, message: "Ажилтны нэр оруулна уу!" }]}
          >
            <Select placeholder="Ажилтны нэр" />
          </Form.Item>

          <Form.Item
            label="Он сар"
            name="date"
            rules={[{ required: false, message: "Он сар оруулна уу!" }]}
          >
            <DatePicker placeholder="Он сар төрөл" />
          </Form.Item>

          <Form.Item
            label="Эхлэх сар өдөр"
            name="startDate"
            rules={[{ required: false, message: "Он сар оруулна уу!" }]}
          >
            <DatePicker placeholder="Он сар төрөл" />
          </Form.Item>

          <Form.Item
            label="Дуусах сар өдөр"
            name="endDate"
            rules={[{ required: false, message: "Он сар оруулна уу!" }]}
          >
            <DatePicker placeholder="Он сар төрөл" />
          </Form.Item>

          <Form.Item
            label="Үндсэн цалин"
            name="salary"
            rules={[{ required: false, message: "Он сар оруулна уу!" }]}
          >
            <Input placeholder="Үндсэн цалин" />
          </Form.Item>

          <Form.Item
            label="Утга"
            name="description"
            rules={[{ required: false, message: "Утга оруулна уу!" }]}
            extra="Та энд утга оруулна уу."
          >
            <Input placeholder="Цалин нэмэх" />
          </Form.Item>
          <Form.Item
            label="Мөнгөн дүн"
            name="amount"
            rules={[{ required: false, message: "Мөнгөн дүнг оруулна уу!" }]}
            extra="Та энд мөнгөн дүнг оруулна уу."
          >
            <Input placeholder="Цалин нэмэх" />
          </Form.Item>

          <Form.Item
            label="Нэмэлт цалин"
            name="additionalAmount"
            rules={[{ required: false, message: "Нэмэлт хэмжээ оруулна уу!" }]}
            extra="Энд нэмэлт цалингийн хэмжээг оруулна уу."
          >
            <Input placeholder="Нэмэлт хэмжээ" />
          </Form.Item>

          <Form.Item className="text-center">
            <Button type="primary" htmlType="submit" className="h-9">
              Цалин бодох
            </Button>
          </Form.Item>
        </Form>
      </Modal>

      <Modal
        title="Дамжлага засах"
        visible={isEditModalVisible}
        onCancel={handleEditModalClose}
        footer={null}
      >
        <Divider />
        {editItem && (
          <Form
            layout="vertical"
            onFinish={handleEditSubmit}
            initialValues={editItem}
          >
            <Form.Item
              label="Ажилтны нэр"
              name="lastname"
              rules={[{ required: false, message: "Ажилтны нэр оруулна уу!" }]}
            >
              <Select placeholder="Ажилтны нэр" />
            </Form.Item>

            <Form.Item
              label="Үндсэн цалин"
              name="salary"
              rules={[{ required: false, message: "Он сар оруулна уу!" }]}
            >
              <Input placeholder="Үндсэн цалин" />
            </Form.Item>

            <Form.Item
              label="Утга"
              name="description"
              rules={[{ required: false, message: "Утга оруулна уу!" }]}
              extra="Та энд утга оруулна уу."
            >
              <Input placeholder="Цалин нэмэх" />
            </Form.Item>

            <Form.Item
              label="Мөнгөн дүн"
              name="amount"
              rules={[{ required: false, message: "Мөнгөн дүнг оруулна уу!" }]}
              extra="Та энд мөнгөн дүнг оруулна уу."
            >
              <Input placeholder="Цалин нэмэх" />
            </Form.Item>

            <Form.Item
              label="Нэмэлт цалин"
              name="additionalAmount"
              rules={[
                { required: false, message: "Нэмэлт хэмжээ оруулна уу!" },
              ]}
              extra="Энд нэмэлт цалингийн хэмжээг оруулна уу."
            >
              <Input placeholder="Нэмэлт хэмжээ" />
            </Form.Item>

            <Form.Item className="text-center">
              <Button type="primary" htmlType="submit" className="h-9">
                Хадгалах
              </Button>
            </Form.Item>
          </Form>
        )}
      </Modal>

      {selectedEmployee && (
        <Modal
          title="Цалингийн дэлгэрэнгүй "
          visible={isModalVisible}
          onCancel={handleModalClose}
          footer={null}
          className="p-4 max-w-lg"
        >
          <Divider />
          <div className="flex flex-col space-y-4">
            <p className="text-sm">
              <span className="font-bold text-gray-700">Овог:</span>{" "}
              {selectedEmployee.firstname}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Нэр:</span>{" "}
              {selectedEmployee.lastname}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Бодогдсон сар:</span>{" "}
              {selectedEmployee.date}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Үндсэн цалин:</span>{" "}
              {selectedEmployee.salary}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">
                Бодогдсон өдөрүүд:
              </span>{" "}
              {selectedEmployee.date}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">
                Ажиллах ёстой өдөр:
              </span>{" "}
              {selectedEmployee.date}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Нэг өдрийн цалин:</span>{" "}
              {selectedEmployee.salary}
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">
                Бодогдсон үндсэн цалин:
              </span>{" "}
              {selectedEmployee.salary}
            </p>
            <Divider>Нэмэгдэл/Урамшуулал</Divider>
            <p className="text-sm">
              <span className="font-bold text-blue-700">
                Нийт нэмэглдийн дүн:
              </span>{" "}
              <p>0 төгрөг</p>
            </p>
            <p>Нийт</p>
            <p className="text-sm">
              <span className="font-bold text-green-500">
                Нийт бодогдсон цалин:
              </span>{" "}
              <p>1,020,160 төгрөг</p>
            </p>
            <Divider>Суутгал</Divider>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Урьдчилгаа:</span>{" "}
              <p>400,000 төгрөг</p>
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">НДШ:</span>{" "}
              <p>51,750 төгрөг</p>
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">ХХОАТ:</span>{" "}
              <p>19,825 төгрөг</p>
            </p>
            <p className="text-sm">
              <span className="font-bold text-red-700">
                Нийт суутгалын дүн:
              </span>{" "}
              <p>471,575 төгрөг</p>
            </p>
            <p className="text-sm">
              <span className="font-bold text-gray-700">Гарт олгох дүн:</span>{" "}
              <p>548,585 төгрөг</p>
            </p>
          </div>
        </Modal>
      )}
    </div>
  );
};

export default SalaryTable;
