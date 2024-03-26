USE [SMCDSS]
GO

/****** Object:  Table [dbo].[ASOReturnItemHistory]    Script Date: 5/30/2018 2:47:25 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[ASOReturnItemHistory](
	[ReturnHistoryID] [bigint] IDENTITY(1,1) NOT NULL,
	[ReturnID] [bigint] NOT NULL,
	[ReturnItemID] [bigint] NOT NULL,
	[ReturnChallanNo] [nchar](10) NOT NULL,
	[ReturnCreateDate] [datetime] NOT NULL,
	[ReturnHistoryCreateDate] [datetime] NOT NULL,
 CONSTRAINT [PK_ASOReturnItemHistory] PRIMARY KEY CLUSTERED 
(
	[ReturnHistoryID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

