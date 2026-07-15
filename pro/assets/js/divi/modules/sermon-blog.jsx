// External Dependencies
import React, {Component} from 'react';

class Sermon_Blog extends Component {

    static slug = 'smp_sermon_blog';

    render() {
        return (
            <div className="smp-sermon-blog">
                {this.props.content()}
            </div>
        );
    }
}

export default Sermon_Blog;
