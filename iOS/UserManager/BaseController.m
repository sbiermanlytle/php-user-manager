//
//  AbstractViewController.m
//  UserManager
//
//  Created by Sebastian Bierman-Lytle on 12/27/14.
//  Copyright (c) 2014 iio interactive. All rights reserved.
//

#import "BaseController.h"

@interface BaseController ()

@end

@implementation BaseController

NSString* const URI = @"http://app-user.iiointeractive.com/remote/";
NSString* const IK = @"4f6d6197e9a292a463f96570fca1721764653c329f6ef97f02515af22c0945a7";

@synthesize debug;
@synthesize bottombar;
@synthesize alert;
@synthesize loadingCircle;
@synthesize loadingSpinner;
@synthesize loadingText;
@synthesize httpdata;
@synthesize sw;
@synthesize sh;
@synthesize ny;
@synthesize nh;
@synthesize color1;

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    CGRect screenRect = [[UIScreen mainScreen] bounds];
    sw = screenRect.size.width;
    sh = screenRect.size.height;
    
    nh = self.navigationController.navigationBar.frame.size.height;
    ny = nh + self.navigationController.navigationBar.frame.origin.y;
    
    color1 = [UIColor colorWithRed:0 green:186.0/255.0 blue:1.0 alpha:1.0];
    
    self.title = @"Home";
    
    [self.view setBackgroundColor:[UIColor whiteColor]];
    
    [self create_loadingSpinner:self.sw/2.5];
    
    debug = [[UILabel alloc] initWithFrame:CGRectMake(0, sh-60, sw, 60)];
    [debug setFont: [UIFont fontWithName:@"Arial" size:14]];
    debug.textColor = [UIColor blackColor];
    debug.textAlignment = NSTextAlignmentCenter;
    debug.backgroundColor = [UIColor clearColor];
    debug.text = @"phpUM";
    
    [self.view addSubview:debug];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

-(void)alert:(NSString*)title message:(NSString*)text
{
    alert = [[UIAlertView alloc] initWithTitle:title message:text delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
    alert.alertViewStyle = UIAlertViewStyleDefault;
    [alert show];
}

-(NSURL*)prepare_query:(NSString*)path params:(NSArray*)params
{
    NSString *query = path;
    for(NSString *param in params){
        query = [query stringByAppendingString:@"/"];
        query = [query stringByAppendingString:[param stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding]];
    }
    return [NSURL URLWithString:[URI stringByAppendingString:query]];
}

-(void)logout
{
    [SSKeychain deletePasswordForService:@"phpUM" account:[[NSUserDefaults standardUserDefaults] stringForKey:@"email"]];
    [[NSUserDefaults standardUserDefaults] removeObjectForKey:@"email"];
    [[NSUserDefaults standardUserDefaults] synchronize];
    [self back];
}

-(void)back
{
    [self.navigationController popViewControllerAnimated:TRUE];
}

-(void)async_task:(SEL)background_task :(SEL)oncomplete_task :(BOOL)show_loading :(NSString*)loading_title
{
    if(show_loading) [self show_loadingSpinner:loading_title];
    
    dispatch_queue_t queue = dispatch_queue_create("com.iio.UserManager", NULL);
    dispatch_async(queue, ^{
        
        ((void (*)(id, SEL))[self methodForSelector:background_task])(self, background_task);
        
        dispatch_async(dispatch_get_main_queue(), ^{
            //code to be executed on the main thread when background task is finished
            
            if(show_loading) [self hide_loadingSpinner];
            
            ((void (*)(id, SEL))[self methodForSelector:oncomplete_task])(self, oncomplete_task);
        });
    });
}

-(UIScrollView*)create_scrollView
{
    UIScrollView *scrollView = [[UIScrollView alloc]initWithFrame:
                  CGRectMake(0, self.ny, self.sw, self.sh-self.ny)];
    scrollView.contentSize = CGSizeMake(self.sw,self.sh);
    [self.view addSubview:scrollView];
    return scrollView;
}

-(UILabel*)create_uilabel:(NSString*)text withFrame:(CGRect)frame
{
    UILabel *label = [[UILabel alloc] initWithFrame:frame];
    [label setFont: [UIFont fontWithName:@"Thonburi" size:18]];
    label.textColor = [UIColor blackColor];
    label.textAlignment = NSTextAlignmentCenter;
    label.backgroundColor = [UIColor clearColor];
    label.text = text;
    label.lineBreakMode = NSLineBreakByWordWrapping;
    label.numberOfLines = 0;
    return label;
}

-(LinkedTextField*)create_inputfield:(NSString*)title withFrame:(CGRect)frame
{
    LinkedTextField *textField = [[LinkedTextField  alloc] initWithFrame:frame];
    textField.borderStyle = UITextBorderStyleRoundedRect;
    textField.contentVerticalAlignment = UIControlContentVerticalAlignmentCenter;
    [textField setFont:[UIFont boldSystemFontOfSize:16]];
    textField.placeholder = title;
    textField.returnKeyType = UIReturnKeyNext;
    return textField;
}

-(void)show_loadingSpinner:(NSString*)text
{
    loadingText.text = text;
    [loadingSpinner startAnimating];
    [self.view addSubview:loadingCircle];
}

-(void)hide_loadingSpinner
{
    [loadingSpinner stopAnimating];
    [loadingCircle removeFromSuperview];
}

-(void)create_loadingSpinner:(CGFloat)diameter
{
    loadingCircle = [[UIView alloc] initWithFrame:CGRectMake(sw/2-diameter/2, ny+20, diameter, diameter)];
    loadingCircle.backgroundColor = [UIColor blackColor];
    loadingCircle.alpha = 0.7;
    loadingCircle.layer.cornerRadius = diameter/2;
    loadingCircle.clipsToBounds = YES;
    
    loadingSpinner = [[UIActivityIndicatorView alloc]initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleWhiteLarge];
    loadingSpinner.frame = CGRectMake(diameter/2-25,diameter/5,50,50);
    [loadingCircle addSubview:loadingSpinner];
    
    loadingText = [[UILabel alloc] initWithFrame:CGRectMake(0, diameter*0.56, diameter, 30)];
    [loadingText setFont: [UIFont fontWithName:@"Arial" size:18]];
    loadingText.textColor = [UIColor whiteColor];
    loadingText.textAlignment = NSTextAlignmentCenter;
    loadingText.backgroundColor = [UIColor clearColor];
    loadingText.text = @"Loading...";
    [loadingCircle addSubview:loadingText];
}

-(void)create_bottombar
{
    //create view
    bottombar = [[UIView alloc] initWithFrame:CGRectMake(0, sh-sh/9, sw, sh/9)];
    bottombar.backgroundColor =  color1;
    
    //back label
    UILabel *back = [[UILabel alloc] initWithFrame:CGRectMake(0, 0, sw, sh/9)];
    [back setFont: [UIFont fontWithName:@"Arial" size:14]];
    back.textColor = [UIColor whiteColor];
    back.font = [UIFont fontWithName:@"TrebuchetMS-Bold" size:18];
    back.textAlignment = NSTextAlignmentCenter;
    back.backgroundColor = [UIColor clearColor];
    back.text = @"Back";
    [bottombar addSubview:back];
    
    //touch handler
    UITapGestureRecognizer *tap = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(back)];
    [bottombar addGestureRecognizer:tap];
    
    [self.view addSubview:bottombar];
}

-(UIButton*)create_button:(NSString*)title :(SEL)callback :(CGRect)frame
{
    //create button
    UIButton *button = [UIButton buttonWithType:UIButtonTypeRoundedRect];
    button.frame = frame;
    
    //callback
    [button addTarget:self
               action:callback
     forControlEvents:UIControlEventTouchUpInside];
    
    //background
    [button setBackgroundColor:color1];
    
    //border
    [button.layer setCornerRadius:20.0f];
    [button.layer setBorderColor:[UIColor whiteColor].CGColor];
    [button.layer setBorderWidth:1.5f];
    
    //title
    [button setTitle:title forState:UIControlStateNormal];
    [button setTitleColor:[UIColor whiteColor] forState:UIControlStateNormal];
    [button setTitleColor:[UIColor blackColor] forState:UIControlStateHighlighted];
    [button.titleLabel setFont:[UIFont fontWithName:@"TrebuchetMS-Bold" size:18]];
    
    //add to view
    return button;
}

@end
