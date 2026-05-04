**free
ctl-opt nomain;

dcl-proc OrderEntry export;
  dcl-pi *n;
    orderId     char(10) value;
    status      char(20);
    customer    varchar(50);
    total       packed(9:2);
    itemCount   int(5);
  end-pi;

  exec sql
    select orderStatus, custName, orderTotal, itemCount
      into :status, :customer, :total, :itemCount
      from orders
     where orderId = :orderId;

  if sqlcode <> 0;
    status = 'NOT FOUND';
    customer = '';
    total = 0;
    itemCount = 0;
  endif;
end-proc;
