**free
ctl-opt nomain;

dcl-proc CustomerInquiry export;
  dcl-pi *n;
    searchKey   char(20) value;
    recordCount int(5);
    outData     varchar(500);
  end-pi;

  dcl-ds customer qualified dim(20);
    custNo  int(5);
    name    varchar(50);
    phone   varchar(20);
    balance packed(9:2);
  end-ds;

  dcl-s i int(5);
  dcl-s sqlText varchar(200);

  // Simulate subfile-based inquiry
  exec sql declare c1 cursor for
    select custno, custname, custphone, custbal
    from customers
    where custname like concat('%', :searchKey, '%')
    fetch first 20 rows only;

  exec sql open c1;
  recordCount = 0;

  exec sql fetch c1 into :customer(1).custNo, :customer(1).name,
    :customer(1).phone, :customer(1).balance;

  dow sqlcode = 0;
    recordCount += 1;
    exec sql fetch c1 into :customer(recordCount + 1).custNo,
      :customer(recordCount + 1).name,
      :customer(recordCount + 1).phone,
      :customer(recordCount + 1).balance;
  enddo;

  exec sql close c1;

  // Serialize to JSON-like string for web consumption
  outData = '';
  for i = 1 to recordCount;
    outData = outData + %trim(customer(i).name) + '|' +
              %char(customer(i).custNo) + '|' +
              %char(customer(i).balance) + '~';
  endfor;
end-proc;
