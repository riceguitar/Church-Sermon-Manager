// External Dependencies
import React, {Component} from 'react';

class Sermon_Taxonomy extends Component {

    static slug = 'smp_sermon_taxonomy';

    render() {
        return (
            <div className="smp-sermon-taxonomy">
                {this.props.content()}
            </div>
        );
    }
}

export default Sermon_Taxonomy;
