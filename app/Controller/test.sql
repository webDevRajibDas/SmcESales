SELECT sum(RptDailyTranBalance.closing_balance) AS closing_balance,
       CASE
           WHEN Product.parent_id is null OR Product.parent_id = 0 THEN Product.id
           ELSE Product.parent_id END           as product_id
FROM [stores] AS [Store] INNER JOIN [rpt_daily_tran_balance] AS [RptDailyTranBalance]
ON ([Store].[id] = [RptDailyTranBalance].[store_id]) INNER JOIN [products] AS [Product] ON ([Product].[id]=[RptDailyTranBalance].[product_id])
WHERE [RptDailyTranBalance].[tran_date] BETWEEN N'2023-01-05'
  and N'2023-01-05'
  AND [Store].[store_type_id] = 2
  AND [Store].[office_id] = 19
GROUP BY CASE WHEN [Product].[parent_id] is null OR [Product].[parent_id]=0 THEN [Product].[id] ELSE [Product].[parent_id]
END


